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

    public function siteCategories(
        ?string $parentId,
        ?string $language = null,
        ?string $slug = null,
    ): array {
        return $this->siteCategoriesResponse($parentId, $language, $slug)->json();
    }

    public function siteProducts(array $query = [], ?string $language = null): array
    {
        return $this->siteProductsResponse($query, $language)->json();
    }

    public function siteProductSearch(array $query = [], ?string $language = null): array
    {
        return $this->siteProductSearchResponse($query, $language)->json();
    }

    public function siteProduct(string $productId, ?string $language = null): array
    {
        return $this->siteProductResponse($productId, $language)->json();
    }

    public function siteSimilarProducts(string $productId, ?string $language = null): array
    {
        return $this->siteSimilarProductsResponse($productId, $language)->json();
    }

    public function counterpartiesMapPoints(array $bounds): array
    {
        return $this->counterpartiesMapPointsResponse($bounds)->json();
    }

    protected function siteCategoriesResponse(
        ?string $parentId,
        ?string $language = null,
        ?string $slug = null,
    ): Response {
        $query = [];

        if ($parentId !== null) {
            $query['parent_id'] = $parentId;
        }

        if ($slug !== null) {
            $query['slug'] = $slug;
        }

        if ($language !== null) {
            $this->withHeaders(['Accept-Language' => $language]);
        }

        return $this->get('/v1/site/categories', $query);
    }

    protected function siteProductsResponse(array $query, ?string $language): Response
    {
        return $this->withLanguage($language)->get('/v1/site', $query);
    }

    protected function siteProductSearchResponse(array $query, ?string $language): Response
    {
        return $this->withLanguage($language)->get('/v1/site/search', $query);
    }

    protected function siteProductResponse(string $productId, ?string $language): Response
    {
        return $this->withLanguage($language)->get("/v1/site/{$productId}");
    }

    protected function siteSimilarProductsResponse(string $productId, ?string $language): Response
    {
        return $this->withLanguage($language)->get("/v1/site/{$productId}/similar-products");
    }

    protected function counterpartiesMapPointsResponse(array $bounds): Response
    {
        return $this->get('/v1/site/counterparties/map-points', [
            'bounds' => $bounds,
        ]);
    }

    /**
     * BaseClient already defaults Accept-Language to the host app's locale;
     * an explicit value overrides it for this one request, after which
     * execute() resets the headers.
     */
    private function withLanguage(?string $language): static
    {
        if ($language !== null) {
            $this->withHeaders(['Accept-Language' => $language]);
        }

        return $this;
    }
}
