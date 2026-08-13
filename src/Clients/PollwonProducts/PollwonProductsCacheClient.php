<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Support\Facades\Cache;

class PollwonProductsCacheClient extends PollwonProductsClient
{
    private const PREFIX = 'products.';
    private const TTL = 34 * 3600;

    public function productServiceSearch(string $name = '', array $parameters = []): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . md5(serialize([$name, $parameters]));

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productServiceSearch($name, $parameters),
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

    public function productsByIds(string $productType = '', array $ids = []): array
    {
        $idsKey = '["'.implode('","', $ids).'"]';

        $key = self::PREFIX . __FUNCTION__ . '.' . $idsKey;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productsByIds($productType, $ids),
            self::TTL
        );
    }
}
