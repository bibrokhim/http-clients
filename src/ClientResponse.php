<?php

namespace Bibrokhim\HttpClients;

use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;

class ClientResponse
{
    public static function make(Response $response): JsonResponse
    {
        return new JsonResponse(
            $response->json(),
            $response->status()
        );
    }
}