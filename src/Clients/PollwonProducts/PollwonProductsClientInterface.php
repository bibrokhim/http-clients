<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

interface PollwonProductsClientInterface
{
    public function productServiceSearch(string $name): array;

    public function product(string $productId): array;

    public function productsByIds(string $productType, array $ids): array;
}