<?php

namespace Bibrokhim\HttpClients\Clients\Rating;

use Bibrokhim\HttpClients\Clients\BaseClient;

class RatingClient extends BaseClient
{
    public function responded(string $uid)
    {
        return $this->post("v1/$uid/responded")->json();
    }

    public function rate(string $uid, array $attrs)
    {
        return $this->patch("v1/$uid", $attrs)->json();
    }
}