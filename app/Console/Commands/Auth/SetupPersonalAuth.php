<?php

namespace App\Console\Commands\Auth;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;

class SetupPersonalAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:setup-personal-client
                            {--email=admin@gmail.com : Default user email}
                            {--password=Admin123_ : Default user password}
                            {--client-name=Auth App : Name for the Passport Client}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up initial personal authentication: create default user and Passport Personal Access Client';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $clientName = $this->option('client-name', 'Auth App');

        DB::beginTransaction();
        try {
            // Create default user if not exists
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst(explode('@', $email)[0]),
                    'password' => Hash::make($password),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->warn('User was not found, so a new user was created.');
                $this->line("Email: {$email}");
                $this->line("Password: <fg=yellow>{$password}</fg=yellow>");
                $this->comment('Please save this password securely. It will not be shown again.');
            } else {
                $this->info("User [{$email}] was set up.");
                $this->info("Found existing user with ID: {$user->id}");
            }

            $this->info("Creating Personal Access Client named '{$clientName}' for user ID {$user->id}...");

            $clientRepo = new ClientRepository;
            $client = $clientRepo->createPersonalAccessGrantClient(
                $clientName,
            );

            $this->info('Personal Access Client created successfully.');

            $this->call('vault:push-oauth-personal-keys', [
                'id' => $client->id,
                'secret' => $client->secret,
            ]);

            $this->call('apisix:push-consumers', [
                'consumerId' => $user->id,
            ]);

            DB::commit();

            // Display the details of the newly created client.
            $this->line('');
            $this->comment('New Client Details:');
            $this->table(
                ['ID', 'Name', 'Secret'],
                [
                    [
                        'ID' => $client->id,
                        'Name' => $client->name,
                        'Secret' => $client->secret,
                    ],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Setup failed: '.$e->getMessage());

            return 1;
        }
    }
}
