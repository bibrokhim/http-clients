<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Support\Facades\Cache;

class PollwonProductsCacheClient extends PollwonProductsClient
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

    public function product(string $productId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$productId";

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::product($productId),
            self::TTL
        );
    }
}