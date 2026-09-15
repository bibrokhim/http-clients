<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\Clients\BaseClient;
use Illuminate\Http\Client\Response;

class PollwonProductsClient extends BaseClient implements PollwonProductsClientInterface
{
    public function productServiceSearch(string $name = '', array $parameters = []): array
    {
        return $this->get('/v1/admin/products/search', array_filter([
            ...$parameters,
            's' => $name,
        ], fn (mixed $value) => $value !== null))->json();
    }

    public function product(string $productId): array
    {
        return $this->get("/v1/catalog/products/$productId")->json();
    }

    public function productsByIds(string $productType = '', array $ids = []): array
    {
        return $this
            ->post('/v1/catalog/products/by-ids', [
                'filter' => [
                    'id' => $ids,
                ],
            ])
            ->json('data');
    }

    public function pollwonSiteProductsByIds(string $productType = '', array $ids = []): array
    {
        return $this
            ->post('/v1/admin/products/by-ids', [
                'filter' => [
                    'id' => $ids,
                ],
            ])
            ->json('data');
    }

    public function productExists(string $productId): array
    {
        return $this->get(
            "/v1/admin/products/{$productId}/exists"
        )->json('data');
    }

    public function siteCategories(?string $parentId, ?string $language = null): array
    {
        return $this->siteCategoriesResponse($parentId, $language)->json();
    }

    public function counterpartiesMapPoints(array $bounds): array
    {
        return $this->counterpartiesMapPointsResponse($bounds)->json();
    }

    protected function siteCategoriesResponse(?string $parentId, ?string $language = null): Response
    {
        $query = $parentId === null ? [] : ['parent_id' => $parentId];

        if ($language !== null) {
            $this->withHeaders(['Accept-Language' => $language]);
        }

        return $this->get('/v1/site/categories', $query);
    }

    protected function counterpartiesMapPointsResponse(array $bounds): Response
    {
        return $this->get('/pollwon-site/v1/site/counterparties/map-points', [
            'bounds' => $bounds,
        ]);
    }
}
