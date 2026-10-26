<?php

namespace Bibrokhim\HttpClients\Clients\ServiceCRM;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Support\Facades\Cache;

class ServiceCrmCacheClient extends ServiceCrmClient
{
    private const PREFIX = 'service-crm.';

    public function serviceCountry(int $serviceId): array
    {
        $key = self::PREFIX.__FUNCTION__.".$serviceId";

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $this->serviceCountryResponse($serviceId);
        $data = $response->json();

        if ($response->successful()) {
            return CacheHelper::store($key, $data, (int) config('http_clients.service_crm.service_country_cache_ttl'));
        }

        return $data;
    }
}
