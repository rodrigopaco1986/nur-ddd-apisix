<?php

namespace App\Console\Commands\Vault;

use App\Services\VaultClient;
use Illuminate\Console\Command;

class PushVaultOauthPersonalKeys extends Command
{
    private $vaultClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vault:push-oauth-personal-keys
                            {id : Client id}
                            {secret : Client secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Oauth Personal keys into Vault';

    public function __construct(VaultClient $vaultClient)
    {
        parent::__construct();
        $this->vaultClient = $vaultClient;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $secret = $this->argument('secret');

        $this->info('→ Pushing vault personal keys...');
        $this->vaultClient->putOauthPersonalKeys($id, $secret);
        $this->info('✔ Done.');
    }
}
