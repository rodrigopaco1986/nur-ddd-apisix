<?php

namespace App\Console\Commands\Apisix;

use App\Services\ApisixClient;
use App\Services\VaultClient;
use Illuminate\Console\Command;

class PushApisixRoutes extends Command
{
    private $apisixClient;

    private $vaultClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apisix:push-routes
                            {--consumer-id=1 : Consumer id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import APISIX routes from config into the gateway';

    public function __construct(ApisixClient $apisixClient, VaultClient $vaultClient)
    {
        parent::__construct();
        $this->apisixClient = $apisixClient;
        $this->vaultClient = $vaultClient;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumerId = (string) $this->option('consumer-id');

        $this->info('→ Pushing APISIX routes...');
        $this->apisixClient->pushRoutes();
        $this->info('✔ Done.');

        $publicOauthToken = $this->vaultClient->getOauthKey('public');
        $this->info('→ Pushing APISIX consumers...');
        $this->apisixClient->pushConsumers($consumerId, $publicOauthToken);
        $this->info('✔ Done.');
    }
}
