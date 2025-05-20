<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ApisixException;

class ApisixClient
{
    protected $baseUri;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUri = config('apisix.base_uri');
        $this->apiKey  = config('apisix.admin_key');
    }

    protected function client()
    {
        return Http::withHeaders([
            'X-API-KEY'    => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUri);
    }

    public function pushRoutes(): void
    {
        $routes = config('apisix.routes');

        foreach ($routes as $id => $def) {
            $res = $this->client()
                ->put("/routes/{$id}", $def);

            if (! $res->successful()) {
                throw new ApisixException(
                    "APISIX route '{$id}' failed: " . $res->status() . ' ' . $res->body()
                );
            }
        }
    }

    public function pushConsumers(string $userKey, string $consumerId, string $jwtAuthCredentials): void
    {
        $res = $this->client()
            ->put("/consumers/{$consumerId}", [
            'username' => $consumerId,
            'plugins' => [
                'jwt-auth' => [
                    'key' => $userKey,
                    'key_claim_name' => 'sub',
                    'algorithm' => 'RS256',
                    'public_key' => $jwtAuthCredentials,
                    'hide_credentials' => false,
                ]
            ]
        ]);
        
        if (! $res->successful()) {
                throw new ApisixException(
                    "APISIX create consumer '{$consumerId}' failed: " . $res->status() . ' ' . $res->body()
                );
        }
    }
}
