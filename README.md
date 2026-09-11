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
    );

    return response()->json($categories);
}
```

### The contract

```php
public function siteCategories(?string $parentId, ?string $language = null): array;
```

- **`$parentId`** — `null` is sent without `parent_id`; any other value is sent
  unchanged as `parent_id`.
- **`$language`** — when supplied, it is sent unchanged as `Accept-Language`.
  The client does not validate, normalise, or replace it. When omitted, the
  package's existing default `Accept-Language` header is used.
- The decoded JSON body is returned as an `array`, in line with the other
  `PollwonProductsClient` methods. Existing `BaseClient` error behaviour is
  unchanged.

### Caching

With `HTTP_CLIENT_CACHE=true` the container binds `PollwonProductsCacheClient`,
which caches under:

```
pollwon-products.site-categories.v1.{language}.{root|parent_id}
```

One key per level per locale, so root and each parent stay independent; `root` is
used for a null parent only, and an empty-string parent gets its own key. Only a
successful response is stored.

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
