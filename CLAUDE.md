# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`bibrokhim/http-clients` is a Laravel package (PSR-4 namespace `Bibrokhim\HttpClients\`) that centralizes typed HTTP clients for talking to a set of internal microservices (CRM, Products, SMS, Firebase, Media, Helpdesk, OneC, ApiGateway, Rating, ServiceCRM, PsSap, Epamarket, Pos, PollwonProducts). It is consumed by other Laravel apps as a dependency, not run standalone.

There is no test suite, build step, or linter configured in this repo (no `phpunit.xml`, no composer `scripts`, no CI config). Changes are validated by the consuming Laravel application(s).

## Commands

- Install dependencies: `composer install`
- There are no `test`, `lint`, or `build` scripts defined in `composer.json`. Do not assume PHPUnit/Pest is set up — check before suggesting `vendor/bin/phpunit`.

## Architecture

### Service provider wiring

`src/HttpClientsServiceProvider.php` is the single place all clients are bound into Laravel's container. Config lives in `config/config.php` and is merged under the `http_clients` key. When adding a new client:

1. Add its `base_url` (and `token` if needed) to `config/config.php`.
2. Bind it in `HttpClientsServiceProvider::register()`, constructing it with values pulled from `config('http_clients.<service>.*')`.

### The client/cache/interface/dev pattern

Most services under `src/Clients/<Service>/` follow one of these shapes — check sibling files before assuming which applies to a new client:

- **Simple client**: just `<Service>Client extends BaseClient` (e.g. `ApiGatewayClient`, `OneCClient`, `HelpdeskClient`, `PsSapClient`, `EpamarketClient`, `RatingClient`). One class, no interface.
- **Interface + concrete + cache decorator**: `<Service>ClientInterface`, `<Service>Client implements <Service>ClientInterface`, and `<Service>CacheClient extends <Service>Client` that overrides every interface method to wrap the parent call with `CacheHelper::store()` (see `Clients/Products`, `Clients/PollwonProducts`, `Clients/CRM`). The service provider picks the cache variant when `config('http_clients.cache')` is truthy. The cache key convention is `"<prefix>." . __FUNCTION__` plus any identifying arguments/query string appended — follow this exactly when adding a cached method so keys stay predictable and unique per parameter set.
- **Interface + prod/dev split**: `<Service>ClientInterface` with a real client for production and a `*DevClient` that no-ops or redirects to a Telegram chat for non-production environments (see `SmsGatewayClient`/`SmsDevClient`, `FirebaseClient`/`FirebaseDevClient`). The service provider chooses based on `app()->isProduction()`, not the `cache` config flag. `FirebaseDevClient` does not extend `BaseClient` — it talks to Telegram directly via the `Http` facade.

### BaseClient (`src/Clients/BaseClient.php`)

All "real" HTTP clients extend this. Key behaviors:

- Wraps `Illuminate\Support\Facades\Http` (`PendingRequest`). `get/post/put/patch/delete` set `$this->method`/`$this->url` then call the private `execute()`, which clones the client, applies headers/attachments, fires the request, and resets state.
- `checkResponse()` throws `ServerErrorException` on 5xx, and `ClientErrorException` on 4xx **only if** `failOnClientErrors()` was called on the client first (4xx is otherwise passed through silently) — both log via `Log::notice` first.
- `withHeaders()`, `attach()` (supports single `UploadedFile` or an array of them), and `failOnClientErrors()` are chainable and reset after each `execute()`.
- `fromUser(User $user)` / `fromBitrixUser(User $user)` inject `X-User-ID`/`X-User-Type`/`X-User-Platform` headers — these reference `App\Models\User` from the **consuming application**, not this package, so they only work when used inside a host Laravel app.
- `async(array $requests)` takes `AsyncRequest` value objects (`src/AsyncRequest.php`, just a `uri` + `method`) and fires them concurrently via `Http::pool`, then runs `checkResponse()` on each result.

### Response handling

Client methods generally return the decoded array (`->json()` or `->json('some.path')`), not the raw `Response` — see `ProductsClient` for the common shape. `src/ClientResponse.php` is a small helper (`ClientResponse::make($response)`) for controllers in the host app that want to proxy a client's raw `Illuminate\Http\Client\Response` straight back out as a `JsonResponse` with the same status code.

### Adding a new client method

Match the existing style in that client's file: build the path/query inline, call `$this->get/post/...()`, and immediately unwrap with `->json(...)`. If the client has a `*CacheClient` counterpart, add the same method there too, following the existing key-naming convention in that file.
