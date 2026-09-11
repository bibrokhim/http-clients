<?php

use Bibrokhim\HttpClients\Clients\PollwonProducts\PollwonProductsCacheClient;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

if (! function_exists('app')) {
    function app(): object
    {
        return PollwonProductsCacheClientTest::application();
    }
}

class PollwonProductsCacheClientTest extends TestCase
{
    private static object $application;

    protected function setUp(): void
    {
        parent::setUp();

        self::$application = new class
        {
            private string $locale = 'uz';

            public function getLocale(): string
            {
                return $this->locale;
            }

            public function setLocale(string $locale): void
            {
                $this->locale = $locale;
            }
        };

        Cache::swap(new Repository(new ArrayStore));
        Http::swap(new Factory);
    }

    public static function application(): object
    {
        return self::$application;
    }

    public function test_pollwon_site_products_by_ids_caches_each_locale_separately(): void
    {
        Http::fake(fn (Request $request) => Http::response([
            'data' => [[
                'id' => 'PRD-1',
                'name' => $request->header('Accept-Language')[0],
            ]],
        ]));

        $this->assertSame(
            [['id' => 'PRD-1', 'name' => 'uz']],
            (new PollwonProductsCacheClient('https://products.test'))
                ->pollwonSiteProductsByIds('product', ['PRD-1'])
        );

        self::$application->setLocale('ru');

        $this->assertSame(
            [['id' => 'PRD-1', 'name' => 'ru']],
            (new PollwonProductsCacheClient('https://products.test'))
                ->pollwonSiteProductsByIds('product', ['PRD-1'])
        );

        self::$application->setLocale('uz');

        $this->assertSame(
            [['id' => 'PRD-1', 'name' => 'uz']],
            (new PollwonProductsCacheClient('https://products.test'))
                ->pollwonSiteProductsByIds('product', ['PRD-1'])
        );

        Http::assertSentCount(2);
    }

    public function test_product_exists_is_not_cached(): void
    {
        Http::fake(Http::response(['data' => ['id' => 'PRD-1', 'exists' => true]]));

        $client = new PollwonProductsCacheClient('https://products.test');

        $client->productExists('PRD-1');
        $client->productExists('PRD-1');

        Http::assertSentCount(2);
    }
}
