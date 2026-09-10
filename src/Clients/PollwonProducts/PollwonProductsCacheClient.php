<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Support\Facades\Cache;

class PollwonProductsCacheClient extends PollwonProductsClient
{
    private const PREFIX = 'products.';

    private const TTL = 34 * 3600;

    private const SITE_CATEGORIES_PREFIX = 'pollwon-products.site-categories.v1';

    private const SITE_CATEGORIES_TTL = 300;

    public function productServiceSearch(string $name = '', array $parameters = []): array
    {
        $key = self::PREFIX.__FUNCTION__.'.'.md5(serialize([$name, $parameters]));

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        return CacheHelper::store(
            $key,
            parent::productServiceSearch($name, $parameters),
            self::TTL
        );
    }

    public function product(string $productId): array
    {
        $key = self::PREFIX.__FUNCTION__.".$productId";

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        return CacheHelper::store(
            $key,
            parent::product($productId),
            self::TTL
        );
    }

    public function productsByIds(string $productType = '', array $ids = []): array
    {
        $idsKey = '["'.implode('","', $ids).'"]';

        $key = self::PREFIX.__FUNCTION__.'.'.$idsKey;

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        return CacheHelper::store(
            $key,
            parent::productsByIds($productType, $ids),
            self::TTL
        );
    }

    public function siteCategories(?string $parentId, ?string $language = null): array
    {
        $key = static::siteCategoriesCacheKey($parentId, $language);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $this->siteCategoriesResponse($parentId, $language);
        $data = $response->json();

        if ($response->successful()) {
            return CacheHelper::store($key, $data, $this->siteCategoriesTtl());
        }

        return $data;
    }

    public static function siteCategoriesCacheKey(?string $parentId, ?string $language): string
    {
        return sprintf(
            '%s.%s.%s',
            self::SITE_CATEGORIES_PREFIX,
            $language ?? app()->getLocale(),
            $parentId ?? 'root'
        );
    }

    private function siteCategoriesTtl(): int
    {
        $ttl = (int) config(
            'http_clients.pollwon_products.site_categories_cache_ttl',
            self::SITE_CATEGORIES_TTL
        );

        return $ttl > 0 ? $ttl : self::SITE_CATEGORIES_TTL;
    }
}
