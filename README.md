# bibrokhim/http-clients

Typed HTTP clients for the internal microservices (CRM, Products, SMS, Firebase,
Media, Helpdesk, OneC, ApiGateway, Rating, ServiceCRM, PsSap, Epamarket, Pos,
PollwonProducts, PollwonSite). Installed as a dependency of the consuming
Laravel applications; it is not run standalone.

```bash
composer require bibrokhim/http-clients
```

Every client is bound in `HttpClientsServiceProvider`, reading `config/config.php`
under the `http_clients` key. Setting `HTTP_CLIENT_CACHE=true` swaps the clients
that have a cache decorator (`Products`, `PollwonProducts`, `CRM`) for their
`*CacheClient` variant.

## Pollwon Product Service — site categories

`GET {POLLWON_PRODUCTS_BASE_URL}/v1/site/categories` backs the public storefront
category tree.

```php
use Bibrokhim\HttpClients\Clients\PollwonProducts\PollwonProductsClientInterface;

public function index(PollwonProductsClientInterface $client, Request $request)
{
    $categories = $client->siteCategories(
        $request->query('parent_id'),   // null for the root level
        $request->header('Accept-Language'),
        $request->query('slug'),
    );

    return response()->json($categories);
}
```

### The contract

```php
public function siteCategories(
    ?string $parentId,
    ?string $language = null,
    ?string $slug = null,
): array;
```

- **`$parentId`** — `null` is sent without `parent_id`; any other value is sent
  unchanged as `parent_id`.
- **`$language`** — when supplied, it is sent unchanged as `Accept-Language`.
  The client does not validate, normalise, or replace it. When omitted, the
  package's existing default `Accept-Language` header is used.
- **`$slug`** — when supplied, it is sent unchanged as `slug`. It can be used
  together with `$parentId`; the client does not validate either lookup value.
- The decoded JSON body is returned as an `array`, in line with the other
  `PollwonProductsClient` methods. Existing `BaseClient` error behaviour is
  unchanged.

### Caching

With `HTTP_CLIENT_CACHE=true` the container binds `PollwonProductsCacheClient`,
which caches under:

```
pollwon-products.site-categories.v1.{language}.{root|lookup}
```

One key per lookup and locale, so root, each parent lookup, and each slug lookup
stay independent; a combined parent-and-slug lookup gets its own key as well.
`root` is used only when both lookups are null, and empty strings remain distinct
values. Only a successful 2xx response is stored.

| Env var                              | Default | Purpose                       |
| ------------------------------------ | ------- | ----------------------------- |
| `POLLWON_PRODUCTS_BASE_URL`          | —       | Service base URL, ending `/api`. |
| `POLLWON_SITE_CATEGORIES_CACHE_TTL`  | `300`   | Cache lifetime, seconds.      |

`POLLWON_PRODUCTS_SITE_CATEGORIES_CACHE_TTL` is the pre-release name for the TTL
and is still read as a fallback; new deployments should set
`POLLWON_SITE_CATEGORIES_CACHE_TTL`.

Cached entries expire on the TTL alone — the package has no knowledge of product
service category events, so a change to a category's `active`, `title`,
`position` or `parent` becomes visible only once the entry lapses. Shorten the
TTL if that window is too wide.

## Pollwon Product Service — storefront products

The four public catalog endpoints the storefront reads. Each one forwards its
query string verbatim and returns the decoded upstream body unchanged, so the
consuming application decides what to validate and what to expose.

```php
use Bibrokhim\HttpClients\Clients\PollwonProducts\PollwonProductsClientInterface;

$client = app(PollwonProductsClientInterface::class);

$list    = $client->siteProducts(['category_slug' => 'nasoslar', 'page' => 2], 'uz');
$search  = $client->siteProductSearch(['search' => 'nasos'], 'ru');
$product = $client->siteProduct('0f8fad5b-d9cb-469f-a165-70867728950e', 'uz');
$similar = $client->siteSimilarProducts('0f8fad5b-d9cb-469f-a165-70867728950e', 'uz');
```

### The contract

```php
public function siteProducts(array $query = [], ?string $language = null): array;
public function siteProductSearch(array $query = [], ?string $language = null): array;
public function siteProduct(string $productId, ?string $language = null): array;
public function siteSimilarProducts(string $productId, ?string $language = null): array;
```

| Method | Upstream path |
| --- | --- |
| `siteProducts` | `GET /v1/site` |
| `siteProductSearch` | `GET /v1/site/search` |
| `siteProduct` | `GET /v1/site/{productId}` |
| `siteSimilarProducts` | `GET /v1/site/{productId}/similar-products` |

- **`$query`** — sent verbatim, including nested parameters such as
  `specifications[<uuid>][]` and `ranges[<uuid>][from]`. The client neither
  validates nor renames anything.
- **`$language`** — when supplied it is sent unchanged as `Accept-Language`;
  otherwise `BaseClient`'s default (the host app's locale) applies.
- The decoded JSON body is returned as an `array`, in line with the other
  `PollwonProductsClient` methods. Existing `BaseClient` error behaviour is
  unchanged: 5xx always throws `ServerErrorException`, and 4xx throws
  `ClientErrorException` only when the caller enabled `failOnClientErrors()`.

### Caching

With `HTTP_CLIENT_CACHE=true` the container binds `PollwonProductsCacheClient`,
which caches under:

```
pollwon-products.site-products.v1.{language}.{all|query.<sha256>}
pollwon-products.site-product-search.v1.{language}.{all|query.<sha256>}
pollwon-products.site-product.v1.{language}.{productId}
pollwon-products.site-similar-products.v1.{language}.{productId}
```

Only a successful 2xx response is stored. The list and search keys hash the
whole parameter set, so every distinct filter, page, sort and locale
combination gets its own entry; key order is normalised, value order is not.

| Env var                           | Default | Purpose                          |
| --------------------------------- | ------- | -------------------------------- |
| `POLLWON_PRODUCTS_BASE_URL`       | —       | Service base URL, ending `/api/pollwon-site`. |
| `POLLWON_SITE_PRODUCTS_CACHE_TTL` | `300`   | Cache lifetime for all four, in seconds. |

## Pollwon Product Service — counterparty map points

`GET {POLLWON_PRODUCTS_BASE_URL}/pollwon-site/v1/site/counterparties/map-points`
returns map points within the supplied bounds.

```php
$points = $client->counterpartiesMapPoints([
    'north' => 41.36,
    'south' => 41.20,
    'east' => 69.38,
    'west' => 69.15,
]);
```

With `HTTP_CLIENT_CACHE=true`, successful responses are cached per bounds for
300 seconds by default. Set `POLLWON_COUNTERPARTIES_MAP_POINTS_CACHE_TTL` to
change the lifetime in seconds. Bound key order does not affect the cache key.

## Development

```bash
composer install
composer test          # PHPUnit
vendor/bin/pint --dirty
composer audit --locked
```

The suite runs on a minimal Illuminate container (`tests/TestApplication.php`)
rather than a full framework install, so the package's production dependency
footprint stays small. `tests/helpers.php` supplies the `app()`, `config()` and
`now()` helpers a host Laravel application would otherwise provide.
