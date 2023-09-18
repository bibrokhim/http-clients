<?php

namespace Bibrokhim\HttpClients;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;

class Response
{
    public static function make(ClientResponse $response): JsonResponse
    {
        return new JsonResponse(
            $response->json(),
            $response->status()
        );
    }
}