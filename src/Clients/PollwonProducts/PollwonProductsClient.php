<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Bibrokhim\HttpClients\Clients\BaseClient;

class PollwonProductsClient extends BaseClient implements PollwonProductsClientInterface
{
    public function productServiceSearch(string $name): array
    {
        return $this->get('/v1/catalog/products-search', [
            's' => $name,
            'resource' => 'search',
        ])->json();
    }
}