<?php

namespace Webkul\Shiprocket\Services;

use Webkul\Core\Models\CoreConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    protected string $baseUrl = 'https://apiv2.shiprocket.in/v1/external';

    /**
     * Get saved config value
     */
    protected function getCredential(string $code): ?string
    {
        return CoreConfig::where('code', $code)
            ->where('channel_code', 'default')
            ->where('locale_code', 'en')
            ->value('value');
    }

    /**
     * Return cached token only
     */
    public function token(): ?string
    {
        return Cache::get('shiprocket_token');
    }

    /**
     * Authenticate & cache token
     */
    public function authenticate(): bool
    {
        $email = trim((string) $this->getCredential('shiprocket.email'));
        $password = trim((string) $this->getCredential('shiprocket.password'));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/auth/login', [
            'email'    => $email,
            'password' => $password,
        ]);

        \Log::info('SHIPROCKET AUTH RESPONSE', $response->json());

        if (! $response->successful()) {
            return false;
        }

        Cache::put('shiprocket_token', $response->json('token'), 3300);
        return true;
    }

    /**
     * Headers with token
     */
    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token(),
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Create Shiprocket Order
     */
    public function createOrder(array $payload): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/orders/create/adhoc', $payload);

        return $response->json();
    }

    
    
    
public function getPrimaryPickupLocation(): ?string
{
    return \Cache::remember('shiprocket_primary_pickup', 86400, function () {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token(),
            'Content-Type'  => 'application/json',
        ])->get($this->baseUrl . '/settings/company/pickup');

        if (! $response->successful()) {
            return null;
        }

        foreach ($response->json('data.shipping_address', []) as $address) {
            if (($address['is_primary_location'] ?? 0) == 1) {
                return $address['pickup_location'];
            }
        }

        return null;
    });
}



}
