<?php

namespace App\Services;

use App\Exceptions\ApisixException;
use Illuminate\Support\Facades\Http;

class ApisixClient
{
    protected $baseUri;

    protected $apiKey;

    public function __construct()
    {
        $this->baseUri = config('apisix.base_uri');
        $this->apiKey = config('apisix.admin_key');
    }

    protected function client()
    {
        return Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])
        // ->withOptions(['debug' => true])
            ->baseUrl($this->baseUri);
    }

    /**
     * Pushes the global rule configurations to APISIX.
     */
    public function pushGlobalRules(): void
    {
        $rules = config('apisix.global_rules');

        if (empty($rules)) {
            return;
        }

        foreach ($rules as $id => $config) {
            $res = $this->client()->put("/global_rules/{$id}", $config);

            if (! $res->successful()) {
                throw new ApisixException(
                    "APISIX global rule '{$id}' update failed: ".$res->status().' '.$res->body()
                );
            }
        }
    }

    public function pushRoutes(): void
    {
        $routes = config('apisix.routes');

        foreach ($routes as $id => $def) {
            $res = $this->client()
                ->put("/routes/{$id}", $def);

            if (! $res->successful()) {
                throw new ApisixException(
                    "APISIX route '{$id}' failed: ".$res->status().' '.$res->body()
                );
            }
        }
    }

    public function pushConsumers(string $consumerId, string $publicOauthToken): void
    {
        $res = $this->client()
            ->put("/consumers/{$consumerId}", [
                'username' => $consumerId,
                'plugins' => [
                    'jwt-auth' => [
                        'key' => $consumerId,
                        'algorithm' => 'RS256',
                        'public_key' => $publicOauthToken,
                    ],
                ],
            ]);

        if (! $res->successful()) {
            throw new ApisixException(
                "APISIX create consumer '{$consumerId}' failed: ".$res->status().' '.$res->body()
            );
        }
    }
}
