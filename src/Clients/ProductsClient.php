<?php

namespace Bibrokhim\HttpClients\Clients;


use Illuminate\Support\Arr;

class ProductsClient extends BaseClient
{
    public function categories()
    {
        return $this->get('v1/product-categories')->json();
    }

    public function sparePartCategories()
    {
        return $this->get('v1/spare-part-categories')->json();
    }

    public function merchCategories()
    {
        return $this->get('v1/merch-categories')->json();
    }

    public function categoryProducts(string $productCategoryId)
    {
        return $this->get("v1/product-categories/$productCategoryId/products", request()->query())->json();
    }

    public function categorySpareParts(string $sparePartCategoryId)
    {
        return $this->get("v1/spare-part-categories/$sparePartCategoryId/spare-parts", request()->query())->json();
    }

    public function categoryMerchProducts(string $merchCategoryId)
    {
        return $this->get("v1/merch-categories/$merchCategoryId/merch-products", request()->query())->json();
    }

    public function dealers()
    {
        return $this->get('v1/dealers')->json();
    }

    public function products()
    {
        return $this->get('v1/products', request()->query())->json();
    }

    public function product(string $productId)
    {
        return $this->get("v1/products/$productId")->json();
    }

    public function productSearch(string $search)
    {
        return $this->get(
            'v1/products-search',
            Arr::add(request()->query(), 'search', $search)
        )->json();
    }

    public function newProducts()
    {
        return $this->get('v1/products-new', request()->query())->json();
    }

    public function hotNewProducts()
    {
        return $this->get('v1/products-hot-new', request()->query())->json();
    }

    public function merchProduct(string $merchProductId)
    {
        return $this->get("v1/merch-products/$merchProductId")->json();
    }
}