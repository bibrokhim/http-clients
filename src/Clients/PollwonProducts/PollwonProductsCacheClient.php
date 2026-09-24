<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;

class PollwonProductsCacheClient extends PollwonProductsClient
{
    private const PREFIX = 'products.';

    private const TTL = 34 * 3600;

    private const SITE_CATEGORIES_PREFIX = 'pollwon-products.site-categories.v1';

    private const SITE_CATEGORIES_TTL = 300;

    private const SITE_PRODUCTS_PREFIX = 'pollwon-products.site-products.v1';

    private const SITE_PRODUCT_PREFIX = 'pollwon-products.site-product.v1';

    private const SITE_SIMILAR_PRODUCTS_PREFIX = 'pollwon-products.site-similar-products.v1';

    private const SITE_PRODUCTS_TTL = 300;

    private const SITE_SITEMAP_PREFIX = 'pollwon-products.site-sitemap.v1';

    private const SITE_SITEMAP_TTL = 1800;

    private const COUNTERPARTIES_MAP_POINTS_PREFIX = 'pollwon-products.counterparties-map-points.v1';

    private const COUNTERPARTIES_MAP_POINTS_TTL = 300;

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

    public function pollwonSiteProductsByIds(string $productType = '', array $ids = []): array
    {
        $idsKey = '["'.implode('","', $ids).'"]';
        $key = self::PREFIX.__FUNCTION__.'.'.app()->getLocale().'.'.$productType.'.'.$idsKey;

        $cached = Cache::get($key);

        if ($cached !== null) {
            return $cached;
        }

        return CacheHelper::store(
            $key,
            parent::pollwonSiteProductsByIds($productType, $ids),
            self::TTL
        );
    }

    public function siteCategories(
        ?string $parentId,
        ?string $language = null,
        ?string $slug = null,
    ): array {
        $key = $this->siteCategoriesCacheKey($parentId, $language, $slug);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $this->siteCategoriesResponse($parentId, $language, $slug);
        $data = $response->json();

        if ($response->successful()) {
            return CacheHelper::store($key, $data, $this->siteCategoriesTtl());
        }

        return $data;
    }

    public function siteProducts(array $query = [], ?string $language = null): array
    {
        return $this->rememberSiteProductResponse(
            $this->siteProductQueryCacheKey(self::SITE_PRODUCTS_PREFIX, $query, $language),
            fn (): Response => $this->siteProductsResponse($query, $language),
        );
    }

    public function siteProductSearch(array $query = [], ?string $language = null): array
    {
        return $this->siteProductSearchResponse($query, $language)->json();
    }

    public function siteProduct(string $productId, ?string $language = null): array
    {
        return $this->rememberSiteProductResponse(
            $this->siteProductIdCacheKey(self::SITE_PRODUCT_PREFIX, $productId, $language),
            fn (): Response => $this->siteProductResponse($productId, $language),
        );
    }

    public function siteSimilarProducts(string $productId, ?string $language = null): array
    {
        return $this->rememberSiteProductResponse(
            $this->siteProductIdCacheKey(self::SITE_SIMILAR_PRODUCTS_PREFIX, $productId, $language),
            fn (): Response => $this->siteSimilarProductsResponse($productId, $language),
        );
    }

    public function siteSitemap(): array
    {
        $key = self::SITE_SITEMAP_PREFIX;

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $this->siteSitemapResponse();
        $data = $response->json();

        if ($response->successful()) {
            return CacheHelper::store($key, $data, $this->siteSitemapTtl());
        }

        return $data;
    }

    public function counterpartiesMapPoints(array $bounds): array
    {
        $key = $this->counterpartiesMapPointsCacheKey($bounds);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $this->counterpartiesMapPointsResponse($bounds);
        $data = $response->json();

        if ($response->successful()) {
            return CacheHelper::store($key, $data, $this->counterpartiesMapPointsTtl());
        }

        return $data;
    }

    private function siteCategoriesCacheKey(?string $parentId, ?string $language, ?string $slug): string
    {
        return sprintf(
            '%s.%s.%s',
            self::SITE_CATEGORIES_PREFIX,
            $language ?? app()->getLocale(),
            $this->siteCategoriesLookupKey($parentId, $slug)
        );
    }

    private function siteCategoriesLookupKey(?string $parentId, ?string $slug): string
    {
        if ($parentId === null && $slug === null) {
            return 'root';
        }

        return 'lookup.'.hash('sha256', serialize([
            'parent_id' => $parentId,
            'slug' => $slug,
        ]));
    }

    private function siteCategoriesTtl(): int
    {
        $ttl = (int) config(
            'http_clients.pollwon_products.site_categories_cache_ttl',
            self::SITE_CATEGORIES_TTL
        );

        return $ttl > 0 ? $ttl : self::SITE_CATEGORIES_TTL;
    }

    /**
     * @param  callable(): Response  $request
     */
    private function rememberSiteProductResponse(string $key, callable $request): array
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $request();
        $data = $response->json();

        if ($response->successful()) {
            return CacheHelper::store($key, $data, $this->siteProductsTtl());
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function siteProductQueryCacheKey(string $prefix, array $query, ?string $language): string
    {
        return sprintf(
            '%s.%s.%s',
            $prefix,
            $language ?? app()->getLocale(),
            $query === [] ? 'all' : 'query.'.hash('sha256', serialize($this->sortQueryKeys($query)))
        );
    }

    private function siteProductIdCacheKey(string $prefix, string $productId, ?string $language): string
    {
        return sprintf('%s.%s.%s', $prefix, $language ?? app()->getLocale(), $productId);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function sortQueryKeys(array $query): array
    {
        ksort($query);

        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $query[$key] = $this->sortQueryKeys($value);
            }
        }

        return $query;
    }

    private function siteProductsTtl(): int
    {
        $ttl = (int) config(
            'http_clients.pollwon_products.site_products_cache_ttl',
            self::SITE_PRODUCTS_TTL
        );

        return $ttl > 0 ? $ttl : self::SITE_PRODUCTS_TTL;
    }

    private function siteSitemapTtl(): int
    {
        $ttl = (int) config(
            'http_clients.pollwon_products.site_sitemap_cache_ttl',
            self::SITE_SITEMAP_TTL
        );

        return $ttl > 0 ? $ttl : self::SITE_SITEMAP_TTL;
    }

    private function counterpartiesMapPointsCacheKey(array $bounds): string
    {
        ksort($bounds);

        return self::COUNTERPARTIES_MAP_POINTS_PREFIX.'.'.md5(serialize($bounds));
    }

    private function counterpartiesMapPointsTtl(): int
    {
        $ttl = (int) config(
            'http_clients.pollwon_products.counterparties_map_points_cache_ttl',
            self::COUNTERPARTIES_MAP_POINTS_TTL
        );

        return $ttl > 0 ? $ttl : self::COUNTERPARTIES_MAP_POINTS_TTL;
    }
}
