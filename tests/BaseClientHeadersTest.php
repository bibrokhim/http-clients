<?php

use Bibrokhim\HttpClients\Clients\PollwonProducts\PollwonProductsCacheClient;
use Bibrokhim\HttpClients\Clients\PollwonProducts\PollwonProductsClient;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;

require __DIR__.'/../vendor/autoload.php';

final class TestApplication implements ArrayAccess
{
    public function __construct(private array $bindings) {}

    public function getLocale(): string
    {
        return 'en';
    }

    public function instance(string $abstract, mixed $instance): mixed
    {
        $this->bindings[$abstract] = $instance;

        return $instance;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->bindings);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->bindings[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->bindings[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->bindings[$offset]);
    }
}

$application = null;

if (! function_exists('app')) {
    function app(?string $abstract = null, array $parameters = []): mixed
    {
        global $application;

        return $abstract === null ? $application : $application[$abstract];
    }
}

function assertTrue(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

function assertProductExistsRequestsHaveDefaultHeaders(string $clientClass): void
{
    global $application;

    $factory = new Factory;
    $application = new TestApplication([Factory::class => $factory]);

    Facade::clearResolvedInstances();
    Facade::setFacadeApplication($application);

    $productId = '550e8400-e29b-41d4-a716-446655440000';
    $url = "https://products.test/api/pollwon-site/v1/admin/products/{$productId}/exists";

    Http::fake([
        $url => Http::response([
            'data' => ['id' => $productId, 'exists' => true],
        ]),
    ]);

    $client = new $clientClass('https://products.test/api');

    assertTrue($client->productExists($productId)['exists'] === true, 'First response should return exists=true.');
    assertTrue($client->productExists($productId)['exists'] === true, 'Second response should return exists=true.');

    $requests = $factory->recorded();
    assertTrue($requests->count() === 2, "{$clientClass} should send two HTTP requests.");

    foreach ($requests as [$request]) {
        assertTrue($request->method() === 'GET', 'The endpoint should use GET.');
        assertTrue($request->url() === $url, 'The endpoint URL should match the Product Service contract.');
        assertTrue($request->hasHeader('Accept', 'application/json'), 'Every request should include Accept: application/json.');
        assertTrue($request->hasHeader('Accept-Language', 'en'), 'Every request should include Accept-Language.');
    }
}

assertProductExistsRequestsHaveDefaultHeaders(PollwonProductsClient::class);
assertProductExistsRequestsHaveDefaultHeaders(PollwonProductsCacheClient::class);

echo "BaseClient default header regression tests passed.\n";
