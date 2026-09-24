<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

interface PollwonProductsClientInterface
{
    public function productServiceSearch(string $name = '', array $parameters = []): array;

    public function product(string $productId): array;

    public function productsByIds(string $productType, array $ids): array;

    public function pollwonSiteProductsByIds(string $productType = '', array $ids = []): array;

    /**
     * @return array{id?: string, exists?: bool}
     */
    public function productExists(string $productId): array;

    public function siteCategories(
        ?string $parentId,
        ?string $language = null,
        ?string $slug = null,
    ): array;

    /**
     * Storefront product list — `GET /v1/site`.
     *
     * @param  array<string, mixed>  $query  sent verbatim as the query string
     */
    public function siteProducts(array $query = [], ?string $language = null): array;

    /**
     * Storefront product search — `GET /v1/site/search`.
     *
     * @param  array<string, mixed>  $query  sent verbatim as the query string
     */
    public function siteProductSearch(array $query = [], ?string $language = null): array;

    /**
     * Storefront product detail — `GET /v1/site/{productId}`.
     */
    public function siteProduct(string $productId, ?string $language = null): array;

    /**
     * Storefront similar products — `GET /v1/site/{productId}/similar-products`.
     */
    public function siteSimilarProducts(string $productId, ?string $language = null): array;

    /**
     * Sitemap product slugs — `GET /v1/site/sitemap`.
     */
    public function siteSitemap(): array;

    /**
     * @param  array{north?: scalar, south?: scalar, east?: scalar, west?: scalar}  $bounds
     */
    public function counterpartiesMapPoints(array $bounds): array;
}
