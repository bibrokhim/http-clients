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

    public function applianceCategories(): array
    {
        $key = self::PREFIX . __FUNCTION__;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::applianceCategories(),
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

    public function categoriesMerchProducts(array $merchCategories): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . implode('-', $merchCategories);

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::categoriesMerchProducts($merchCategories),
            self::TTL
        );
    }

    public function categoryApplianceProducts(string $applianceCategoryId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$applianceCategoryId." . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::categoryApplianceProducts($applianceCategoryId),
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

    public function productsByIds(string $productType, array $ids): array
    {
        $idsKey = '["'.implode('","', $ids).'"]';

        $key = self::PREFIX . __FUNCTION__ . '.' . $productType . '.' . $idsKey;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productsByIds($productType, $ids),
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

    public function serviceCostSearch(string $search): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . $search;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::serviceCostSearch($search),
            self::TTL
        );
    }

    public function paginateProducts(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->input('page');

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::paginateProducts(),
            self::TTL
        );
    }

    public function sparePartSearch(string $search): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . $search;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::sparePartSearch($search),
            self::TTL
        );
    }

    public function paginateSpareParts(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->input('page');

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::paginateSpareParts(),
            self::TTL
        );
    }

    public function sparePartsById(array $ids): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . implode('-', $ids);

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::sparePartsById($ids),
            self::TTL
        );
    }

    public function productCategorySearch(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productCategorySearch(),
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

    public function applianceProduct(string $applianceProductId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$applianceProductId";

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::applianceProduct($applianceProductId),
            self::TTL
        );
    }

    public function applianceProductSearch(): array
    {
        $key = self::PREFIX . __FUNCTION__ . '.' . request()->getQueryString();

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::applianceProductSearch(),
            self::TTL
        );
    }

    public function productStock(string $productId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$productId";

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::productStock($productId),
            self::TTL
        );
    }

    public function merchProductStock(string $merchProductId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$merchProductId";

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::merchProductStock($merchProductId),
            self::TTL
        );
    }

    public function warehouse(string $warehouseId): array
    {
        $key = self::PREFIX . __FUNCTION__ . ".$warehouseId";

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::warehouse($warehouseId),
            self::TTL
        );
    }

    public function usdRate(): ?string
    {
        $key = self::PREFIX . __FUNCTION__;

        if (Cache::has($key)) return Cache::get($key);

        return CacheHelper::store(
            $key,
            parent::usdRate(),
            self::TTL
        );
    }
}