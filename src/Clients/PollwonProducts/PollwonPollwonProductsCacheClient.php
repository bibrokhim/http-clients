<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Support\Facades\Cache;

class PollwonPollwonProductsCacheClient extends PollwonProductsClient
{
    private const PREFIX = 'products.';
    private const TTL = 34 * 3600;

    public function productServiceSearch(string $name): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . $name;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productServiceSearch($name),
            self::TTL
        );
    }
}