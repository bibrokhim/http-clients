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
        $request->getPreferredLanguage(['uz', 'ru', 'en']),
    );

    return response()->json($categories->payload, $categories->status)
        ->withHeaders(array_filter([
            'Content-Language' => $categories->contentLanguage,
            'Retry-After' => $categories->retryAfter,
        ]));
}
```

### The contract

```php
public function siteCategories(?string $parentId, ?string $language = null): SiteCategoriesResponse;
```

- **`$parentId`** — only `null` means the root level. Any other string, an empty
  one included, is sent downstream as `parent_id` and the product service rules
  on it. An empty string is never promoted to a root lookup.
- **`$language`** — optional. `null`, empty and anything outside `uz` / `ru` /
  `en` all normalise to `uz`, and the result is sent as `Accept-Language`. This
  is request-side only.

`SiteCategoriesResponse` holds the downstream answer as it arrived:

| Property          | Notes                                                            |
| ----------------- | ---------------------------------------------------------------- |
| `status`          | Original upstream status code.                                    |
| `payload`         | Decoded JSON body — `null` when the body was not JSON.            |
| `contentLanguage` | `Content-Language` verbatim, `null` when the upstream sent none.  |
| `retryAfter`      | `Retry-After` verbatim — delta-seconds *or* an HTTP-date.         |

Nothing is normalised or defaulted on the way back: a `Content-Language: en-US`
is returned as `en-US`, and a header the upstream never sent stays `null` rather
than being filled in with the requested language. `successful()`, `clientError()`
and `serverError()` classify the status; `cacheable()` is true only for a 200
with a decoded body.

404, 422 and 429 are **returned, not thrown**, so the caller can proxy the
original status, body and headers. 5xx throws `ServerErrorException` and
connection failures keep the package's standard behaviour, both by way of the
usual `BaseClient` rules.

### Caching

With `HTTP_CLIENT_CACHE=true` the container binds `PollwonProductsCacheClient`,
which caches under:

```
pollwon-products.site-categories.v1.{language}.{root|parent_id}
```

One key per level per locale, so root and each parent stay independent; `root` is
used for a null parent only, and an empty-string parent gets its own key. Only a
200 is stored — 404, 422, 429 and a non-JSON body are served to the caller but
never cached.

A cold key is filled behind `Cache::lock()`: the holder re-checks the cache once
it has the lock, so a caller that waited reads the entry the holder wrote instead
of issuing its own request. If the cache store is unreachable the client bypasses
the cache and serves a single fresh response rather than failing; if the lock
itself times out it raises a 503 `ServerErrorException` rather than adding a
second concurrent request to a service that is already busy.

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
