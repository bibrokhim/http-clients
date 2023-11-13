<?php

namespace Bibrokhim\HttpClients;

class AsyncRequest
{
    public string $method;
    public string $uri;

    public function __construct(string $uri, string $method = 'get')
    {
        $this->uri = $uri;
        $this->method = $method;
    }
}