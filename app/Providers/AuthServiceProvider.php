<?php

namespace App\Providers;

use App\Services\VaultClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $command = $this->app->request->server('argv')[1] ?? null;

            $setupCommands = [
                'migrate',
                'migrate:fresh',
                'migrate:install',
                'db:seed',
                'passport:keys',
                'passport:install',
                'passport:create-personal-client', // command that generates keys
                'vault:push-oauth-keys',
            ];

            if (in_array($command, $setupCommands)) {
                return;
            }
        }

        if (config('passport.keys_from_vault')) {
            try {

                $vaultClient = new VaultClient;
                $privateKey = $vaultClient->getOauthKey('private');
                $publicKey = $vaultClient->getOauthKey('public');

                if (empty($privateKey) || empty($publicKey)) {
                    throw new \Exception('Private or public key is empty after fetching from Vault.');
                }

                config([
                    'passport.private_key' => $privateKey,
                    'passport.public_key' => $publicKey,
                ]);

            } catch (\Exception $e) {
                Log::critical('A critical error occurred while trying to load Passport keys from Vault.', [
                    'error' => $e->getMessage(),
                ]);

                abort(500, 'Could not load application encryption keys.');
            }
        }
    }
}
