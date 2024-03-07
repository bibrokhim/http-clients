<?php

namespace Bibrokhim\HttpClients\Clients\Products;

interface ProductsClientInterface
{
    public function categories(): array;

    public function sparePartCategories(): array;

    public function merchCategories(): array;

    public function applianceCategories(): array;

    public function categoryProducts(string $productCategoryId): array;

    public function categorySpareParts(string $sparePartCategoryId): array;

    public function categoryMerchProducts(string $merchCategoryId): array;

    public function categoriesMerchProducts(array $merchCategories): array;

    public function categoryApplianceProducts(string $applianceCategoryId): array;

    public function dealers(): array;

    public function products(): array;

    public function productsByIds(string $productType, array $ids): array;

    public function product(string $productId): array;

    public function productSearch(): array;

    public function newProducts(): array;

    public function hotNewProducts(): array;

    public function merchProduct(string $merchProductId): array;

    public function applianceProduct(string $applianceProductId): array;

    public function productStock(string $productId): array;

    public function merchProductStock(string $merchProductId): array;

    public function warehouse(string $warehouseId): array;

    public function usdRate(): ?string;
}