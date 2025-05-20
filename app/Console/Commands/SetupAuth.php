<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SetupAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:setup
                            {--email=admin@gmail.com : Default user email}
                            {--password=Admin123_       : Default user password}
                            {--client-name=APISIX Password Client : Name for the Passport password grant client}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up initial authentication: create default user and Passport password grant client';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $clientName = $this->option('client-name');

        DB::beginTransaction();
        try {
            // Create default user if not exists
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => ucfirst(explode('@', $email)[0]),
                    'password' => Hash::make($password),
                ]
            );

            $this->info("User [{$email}] is set up.");

            // Create Passport Password Grant Client
            $clientRepo = new ClientRepository();
            $client = $clientRepo->createPasswordGrantClient(
                $clientName,    // client name
                null,           // user provider (null for default)
                true            // confidential client
            );

            $this->components->info('New client created successfully.');
            if ($client) {
                $this->components->twoColumnDetail('Client ID', $client->getKey());

                if ($client->confidential()) {
                    $this->components->twoColumnDetail('User Key', $user->getAuthIdentifier());
                    $this->components->twoColumnDetail('Client Secret', $client->plainSecret);
                    $this->components->warn('The client secret will not be shown again, so don\'t lose it!');
                }
            }
            
            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Setup failed: ' . $e->getMessage());
            return 1;
        }
    }
}
