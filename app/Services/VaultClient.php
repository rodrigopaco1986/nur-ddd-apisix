<?php

namespace App\Services;

use App\Exceptions\VaultException;
use Illuminate\Support\Facades\Http;

class VaultClient
{
    protected $baseUri;

    protected $token;

    public function __construct()
    {
        $this->baseUri = config('vault.base_uri');
        $this->token = config('vault.root_token');
    }

    protected function client()
    {
        return Http::withHeaders([
            'X-Vault-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])
        // ->withOptions(['debug' => true])
            ->baseUrl($this->baseUri);
    }

    public function getOauthKeys(): array
    {
        $res = $this->client()
            ->get('/v1/secret/data/data/apisix/oauth');

        if (! $res->successful()) {
            throw new VaultException(
                'Vault route /v1/secret/data/data/apisix/oauth failed: '.$res->status().' '.$res->body()
            );
        }

        return json_decode($res->body(), true, 10, JSON_THROW_ON_ERROR);
    }

    public function getOauthKey(string $type = 'public'): string
    {
        $keys = $this->getOauthKeys();

        return $keys['data']['data'][$type] ?? '';
    }

    public function putOauthKeys(string $publicKey, string $privateKey): void
    {
        $res = $this->client()
            ->post('/v1/secret/data/data/apisix/oauth', [
                'data' => [
                    'public' => $publicKey,
                    'private' => $privateKey,
                ],
            ]);

        if (! $res->successful()) {
            throw new VaultException(
                'Vault posting oauth keys failed: '.$res->status().' '.$res->body()
            );
        }
    }
}
