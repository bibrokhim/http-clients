<?php

namespace Bibrokhim\HttpClients\Clients\PollwonProducts;

use Illuminate\Http\Client\Response;

final class SiteCategoriesResponse
{
    /**
     * @param  int  $status  Original HTTP status code.
     * @param  array|null  $payload  Decoded JSON body, or null when the body was not JSON.
     * @param  string|null  $contentLanguage  `Content-Language` verbatim, null when absent.
     * @param  string|null  $retryAfter  `Retry-After` verbatim — delta-seconds or an HTTP-date — null when absent.
     */
    public function __construct(
        public readonly int $status,
        public readonly ?array $payload,
        public readonly ?string $contentLanguage,
        public readonly ?string $retryAfter,
    ) {}

    public static function fromResponse(Response $response): self
    {
        $payload = $response->json();

        return new self(
            $response->status(),
            is_array($payload) ? $payload : null,
            self::header($response, 'Content-Language'),
            self::header($response, 'Retry-After'),
        );
    }

    /**
     * Rehydrate a response previously flattened with {@see self::toArray()}.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            is_int($data['status'] ?? null) ? $data['status'] : 0,
            is_array($data['payload'] ?? null) ? $data['payload'] : null,
            is_string($data['content_language'] ?? null) ? $data['content_language'] : null,
            is_string($data['retry_after'] ?? null) ? $data['retry_after'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'payload' => $this->payload,
            'content_language' => $this->contentLanguage,
            'retry_after' => $this->retryAfter,
        ];
    }

    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function clientError(): bool
    {
        return $this->status >= 400 && $this->status < 500;
    }

    public function serverError(): bool
    {
        return $this->status >= 500 && $this->status < 600;
    }

    /**
     * Only a plain 200 carrying a decoded body is worth storing: 404, 422 and
     * 429 are answers about one moment, not about the category tree.
     */
    public function cacheable(): bool
    {
        return $this->status === 200 && is_array($this->payload);
    }

    private static function header(Response $response, string $name): ?string
    {
        $value = $response->header($name);

        return $value === '' ? null : $value;
    }
}
