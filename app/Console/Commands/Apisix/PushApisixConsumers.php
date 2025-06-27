<?php

namespace App\Console\Commands\Apisix;

use App\Services\ApisixClient;
use App\Services\VaultClient;
use Illuminate\Console\Command;

class PushApisixConsumers extends Command
{
    private $apisixClient;

    private $vaultClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apisix:push-consumers
                            {consumerId : Consumer id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import APISIX consumer into the gateway';

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
        $consumerId = (string) $this->argument('consumerId');

        $publicOauthToken = $this->vaultClient->getOauthKey('public');
        $this->info('→ Pushing APISIX consumers...');
        $this->apisixClient->pushConsumers($consumerId, $publicOauthToken);
        $this->info('✔ Done.');
    }
}
