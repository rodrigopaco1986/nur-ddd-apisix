<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApisixClient;

class PushApisixRoutes extends Command
{
    private $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apisix:push-routes
                            {--user-key=1 : User key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import APISIX routes from config into the gateway';

    public function __construct(ApisixClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userKey = (string)$this->option('user-key');

        $this->info('→ Pushing APISIX routes...');
        $this->client->pushRoutes();
        $this->info('✔ Done.');

        $consumerId = 'passport_users';
        $jwtAuthCredentials = file_get_contents(storage_path('oauth-public.key'));
        $this->info('→ Pushing APISIX consumers...');
        $this->client->pushConsumers($userKey, $consumerId, $jwtAuthCredentials);
        $this->info('✔ Done.');
    }
}
