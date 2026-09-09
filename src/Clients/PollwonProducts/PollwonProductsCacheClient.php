<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\CacheHelper;
use Bibrokhim\HttpClients\Exceptions\ServerErrorException;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Throwable;

class PollwonProductsCacheClient extends PollwonProductsClient
{
    private const PREFIX = 'products.';

    private const TTL = 34 * 3600;

    private const SITE_CATEGORIES_PREFIX = 'pollwon-products.site-categories.v1';

    private const SITE_CATEGORIES_TTL = 300;

    /** Held long enough to outlast the request it guards, short enough to clear a crashed holder. */
    private const SITE_CATEGORIES_LOCK_TTL = 30;

    /** How long a waiting caller blocks before giving up on the holder. */
    private const SITE_CATEGORIES_LOCK_WAIT = 10;

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

    /**
     * @throws ServerErrorException
     */
    public function siteCategories(?string $parentId, ?string $language = null): SiteCategoriesResponse
    {
        $language = static::normalizeLanguage($language);
        $key = static::siteCategoriesCacheKey($parentId, $language);

        try {
            $cached = Cache::get($key);
        } catch (Throwable $e) {
            // The cache store is unreachable — serve traffic, skip caching.
            return parent::siteCategories($parentId, $language);
        }

        if (is_array($cached)) {
            return SiteCategoriesResponse::fromArray($cached);
        }

        return $this->siteCategoriesThroughLock($key, $parentId, $language);
    }

    public static function siteCategoriesCacheKey(?string $parentId, ?string $language): string
    {
        return sprintf(
            '%s.%s.%s',
            self::SITE_CATEGORIES_PREFIX,
            static::normalizeLanguage($language),
            $parentId ?? 'root'
        );
    }

    /**
     * @throws ServerErrorException
     */
    private function siteCategoriesThroughLock(string $key, ?string $parentId, string $language): SiteCategoriesResponse
    {
        try {
            $lock = Cache::lock($key.'.lock', self::SITE_CATEGORIES_LOCK_TTL);
            $lock->block(self::SITE_CATEGORIES_LOCK_WAIT);
        } catch (LockTimeoutException $e) {
            // Deliberately no second downstream request: the point of the lock is
            // that a cold key never fans out into a stampede.
            throw new ServerErrorException('Site categories are temporarily unavailable.', 503);
        } catch (Throwable $e) {
            return parent::siteCategories($parentId, $language);
        }

        try {
            return $this->siteCategoriesUnderLock($key, $parentId, $language);
        } finally {
            $this->release($lock);
        }
    }

    /**
     * @throws ServerErrorException
     */
    private function siteCategoriesUnderLock(string $key, ?string $parentId, string $language): SiteCategoriesResponse
    {
        // Re-check: whoever held the lock has filled the key by now.
        try {
            $cached = Cache::get($key);
        } catch (Throwable $e) {
            $cached = null;
        }

        if (is_array($cached)) {
            return SiteCategoriesResponse::fromArray($cached);
        }

        $response = parent::siteCategories($parentId, $language);

        // 200 only. 404, 422 and 429 are returned to the caller but never stored,
        // and 5xx / connection failures threw before reaching here.
        if ($response->cacheable()) {
            try {
                Cache::put($key, $response->toArray(), $this->siteCategoriesTtl());
            } catch (Throwable $e) {
                // A cache write failure must not fail a request already answered.
            }
        }

        return $response;
    }

    private function siteCategoriesTtl(): int
    {
        $ttl = (int) config(
            'http_clients.pollwon_products.site_categories_cache_ttl',
            self::SITE_CATEGORIES_TTL
        );

        return $ttl > 0 ? $ttl : self::SITE_CATEGORIES_TTL;
    }

    private function release(Lock $lock): void
    {
        try {
            $lock->release();
        } catch (Throwable $e) {
            // The lock TTL will clear it.
        }
    }
}
