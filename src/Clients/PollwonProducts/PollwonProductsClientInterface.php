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
     * @param  array{north?: scalar, south?: scalar, east?: scalar, west?: scalar}  $bounds
     */
    public function counterpartiesMapPoints(array $bounds): array;
}
