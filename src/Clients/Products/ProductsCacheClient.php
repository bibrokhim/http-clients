<?php

namespace Bibrokhim\HttpClients\Clients\Products;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Support\Facades\Cache;

class ProductsCacheClient extends ProductsClient
{
    private const PREFIX = 'products.';
    private const TTL = 34 * 3600;

    public function categories(): array
    {
        $key = self::PREFIX . __FUNCTION__;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::categories(),
            self::TTL
        );
    }

    public function sparePartCategories(): array
    {
        $key = self::PREFIX . __FUNCTION__;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::sparePartCategories(),
            self::TTL
        );
    }

    public function merchCategories(): array
    {
        $key = self::PREFIX . __FUNCTION__;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::merchCategories(),
            self::TTL
        );
    }

    public function categoryProducts(string $productCategoryId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$productCategoryId." . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::categoryProducts($productCategoryId),
            self::TTL
        );
    }

    public function categorySpareParts(string $sparePartCategoryId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$sparePartCategoryId." . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::categorySpareParts($sparePartCategoryId),
            self::TTL
        );
    }

    public function categoryMerchProducts(string $merchCategoryId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$merchCategoryId." . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::categoryMerchProducts($merchCategoryId),
            self::TTL
        );
    }

    public function dealers(): array
    {
        $key = self::PREFIX . __FUNCTION__;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::dealers(),
            self::TTL
        );
    }

    public function products(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::products(),
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

    public function productSearch(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productSearch(),
            self::TTL
        );
    }

    public function newProducts(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::newProducts(),
            self::TTL
        );
    }

    public function hotNewProducts(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::hotNewProducts(),
            self::TTL
        );
    }

    public function merchProduct(string $merchProductId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$merchProductId";

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::merchProduct($merchProductId),
            self::TTL
        );
    }
}