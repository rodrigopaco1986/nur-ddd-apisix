<?php

namespace App\Console\Commands;

use App\Services\VaultClient;
use Illuminate\Console\Command;

class PushVaultOauthKeys extends Command
{
    private $vaultClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vault:push-oauth-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Oauth keys into Vault';

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
        $publicKey = file_get_contents(storage_path('oauth-public.key'));
        $privateKey = file_get_contents(storage_path('oauth-private.key'));

        $this->info('→ Pushing vault keys...');
        $this->vaultClient->putOauthKeys($publicKey, $privateKey);
        $this->info('✔ Done.');
    }
}
