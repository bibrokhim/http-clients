<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\Clients\BaseClient;
use Bibrokhim\HttpClients\Exceptions\ServerErrorException;

class PollwonProductsClient extends BaseClient implements PollwonProductsClientInterface
{
    public const SITE_CATEGORIES_URI = '/v1/site/categories';

    public const SUPPORTED_LANGUAGES = ['uz', 'ru', 'en'];

    public const DEFAULT_LANGUAGE = 'uz';

    public function productServiceSearch(string $name): array
    {
        return $this->get('/v1/catalog/products-search', [
            's' => $name,
            'resource' => 'search',
        ])->json();
    }

    public function product(string $productId): array
    {
        return $this->get("/v1/catalog/products/$productId")->json();
    }

    public function productsByIds(string $productType = '', array $ids = []): array
    {
        return $this
            ->post('v1/catalog/products-collection', [
                'filter' => [
                    'id' => $ids,
                ],
            ])
            ->json('data');
    }

    public function pollwonSiteProductServiceSearch(string $name = '', array $parameters = []): array
    {
        return $this->get('/v1/admin/products/search', array_filter([
            ...$parameters,
            's' => $name,
        ], fn (mixed $value) => $value !== null))->json();
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
            "pollwon-site/v1/admin/products/{$productId}/exists"
        )->json('data');
    }

    /**
     * @throws ServerErrorException
     */
    public function siteCategories(?string $parentId, ?string $language = null): SiteCategoriesResponse
    {
        // Only a null parent means "root". An empty string is a value the caller
        // supplied, so it goes downstream and lets the product service rule on it.
        $query = $parentId === null ? [] : ['parent_id' => $parentId];

        $response = $this->withoutFailingOnClientErrors(
            fn () => $this
                ->withHeaders(['Accept-Language' => static::normalizeLanguage($language)])
                ->get(self::SITE_CATEGORIES_URI, $query)
        );

        return SiteCategoriesResponse::fromResponse($response);
    }

    public static function normalizeLanguage(?string $language): string
    {
        $language = strtolower(trim((string) $language));

        return in_array($language, self::SUPPORTED_LANGUAGES, true)
            ? $language
            : self::DEFAULT_LANGUAGE;
    }

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    private function withoutFailingOnClientErrors(callable $callback)
    {
        $previous = $this->failOnClientErrors;
        $this->failOnClientErrors = false;

        try {
            return $callback();
        } finally {
            $this->failOnClientErrors = $previous;
        }
    }
}
