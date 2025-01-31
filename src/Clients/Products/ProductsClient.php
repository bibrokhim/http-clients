<?php

namespace Bibrokhim\HttpClients\Clients\Products;


use Bibrokhim\HttpClients\Clients\BaseClient;

class ProductsClient extends BaseClient implements ProductsClientInterface
{
    public function categories(): array
    {
        return $this->get('v1/product-categories', request()->query())->json();
    }

    public function sparePartCategories(): array
    {
        return $this->get('v1/spare-part-categories')->json();
    }

    public function merchCategories(): array
    {
        return $this->get('v1/merch-categories')->json();
    }

    public function applianceCategories(): array
    {
        return $this->get('v1/appliances/categories')->json();
    }

    public function categoryProducts(string $productCategoryId): array
    {
        return $this->get("v1/product-categories/$productCategoryId/products", request()->query())->json();
    }

    public function categorySpareParts(string $sparePartCategoryId): array
    {
        return $this->get("v1/spare-part-categories/$sparePartCategoryId/spare-parts", request()->query())->json();
    }

    public function categoryMerchProducts(string $merchCategoryId): array
    {
        return $this->get("v1/merch-categories/$merchCategoryId/merch-products", request()->query())->json();
    }

    public function categoriesMerchProducts(array $merchCategories): array
    {
        $queryString = '["' . implode('","', $merchCategories) . '"]';

        return $this->get("v1/merch-categories-ids/merch-products?category_ids=$queryString")->json();
    }

    public function categoryApplianceProducts(string $applianceCategoryId): array
    {
        return $this->get("v1/appliances/categories/$applianceCategoryId/products", request()->query())->json();
    }

    public function dealers(): array
    {
        return $this->get('v1/dealers')->json();
    } // TODO: server error when 4xx

    public function products(): array
    {
        return $this->get('v1/products', request()->query())->json();
    }

    public function productsByIds(string $productType, array $ids): array
    {
        return $this
            ->post("v1/products/$productType/products-ids", compact('ids'))
            ->json('data');
    }

    public function product(string $productId): array
    {
        return $this->get("v1/products/$productId")->json();
    }

    public function productSearch(): array
    {
        return $this->get('v1/products-search', request()->query())->json();
    }

    public function productServiceSearch(string $name): array
    {
        return $this->get('/v1/service/products', [
            'filter' => [
                'name' => $name,
            ],
        ])->json();
    }

    public function serviceCostSearch(string $search): array
    {
        return $this->get('v1/service/service-costs', [
            'filter' => [
                'name' => $search,
            ]
        ])->json();
    }

    public function paginateProducts(): array
    {
        return $this->get('v1/common/products', [
            'pagination' => 1,
            'page' => request()->input('page', 1),
        ])->json();
    }

    public function sparePartSearch(string $search): array
    {
        return $this->get('v1/common/spare-parts', [
            'filter' => [
                'name' => $search,
            ]
        ])->json();
    }

    public function paginateSpareParts(): array
    {
        return $this->get('v1/common/spare-parts', [
            'pagination' => 1,
            'page' => request()->input('page', 1),
        ])->json();
    }

    public function sparePartsById(array $ids): array
    {
        return $this->get('v1/common/spare-parts', [
            'filter' => [
                'id' => implode(',', $ids),
            ]
        ])->json();
    }

    public function serviceCostsById(array $ids): array
    {
        return $this->get('v1/service/service-costs', [
            'filter' => [
                'id' => implode(',', $ids),
            ]
        ])->json();
    }

    public function productCategorySearch(): array
    {
        return $this->get('v1/product-categories-search', request()->query())->json();
    }

    public function newProducts(): array
    {
        return $this->get('v1/products-new', request()->query())->json();
    }

    public function hotNewProducts(): array
    {
        return $this->get('v1/products-hot-new', request()->query())->json();
    }

    public function merchProduct(string $merchProductId): array
    {
        return $this->get("v1/merch-products/$merchProductId")->json();
    }

    public function applianceProduct(string $applianceProductId): array
    {
        return $this->get("v1/appliances/products/$applianceProductId")->json();
    }

    public function applianceProductSearch(): array
    {
        return $this->get('v1/appliances/products-search', request()->query())->json();
    }

    public function productStock(string $productId): array
    {
        return $this->get("v1/products/$productId/warehouses")->json();
    }

    public function merchProductStock(string $merchProductId): array
    {
        return $this->get("v1/merch-products/$merchProductId/warehouses")->json();
    }

    public function warehouse(string $warehouseId): array
    {
        return $this->get("v1/common/warehouses/$warehouseId")->json();
    }

    public function usdRate(): ?string
    {
        return $this->get("currencies/usd-rate")->json('multiplicity');
    }

    public function getCommissioner(string $cardNumber): ?array
    {
        $response = $this->get("v1/common/comissioner-card-number/$cardNumber");

        if ($response->status() !== 200) {
            return null;
        }

        return $response->json();
    }
}