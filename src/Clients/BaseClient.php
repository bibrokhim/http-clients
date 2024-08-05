<?php

namespace Bibrokhim\HttpClients\Clients;

use App\Models\User;
use Bibrokhim\HttpClients\AsyncRequest;
use Bibrokhim\HttpClients\Exceptions\ClientErrorException;
use Bibrokhim\HttpClients\Exceptions\ServerErrorException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseClient
{
    protected ?string $method;
    protected ?string $url;
    protected string $baseUrl;
    protected array $headers = [
        'Accept' => 'application/json',
    ];
    protected array $attachments = [];
    protected PendingRequest $client;
    protected bool $failOnClientErrors = false;

    public function __construct(string $baseUrl, ?string $token = null)
    {
        $this->client = Http::baseUrl($baseUrl);
        $this->baseUrl = $baseUrl;

        if (isset($token)) {
            $this->client->withToken($token);
        }

        $this->headers = array_merge(
            $this->headers,
            ['Accept-Language' => app()->getLocale()]
        );
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    public function get(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'get';

        return $this->execute($data);
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    public function post(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'post';

        return $this->execute($data);
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    public function patch(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'patch';

        return $this->execute($data);
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    public function put(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'put';

        return $this->execute($data);
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    public function delete(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'delete';

        return $this->execute($data);
    }

    public function withHeaders(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function attach(UploadedFile|array $file, string $name = 'attachment'): static
    {
        $this->attachments[$name] = $file;

        return $this;
    }

    public function failOnClientErrors(): static
    {
        $this->failOnClientErrors = true;

        return $this;
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    public function async(array $requests)
    {
        $client = (clone $this->client)->withHeaders($this->headers);
        $asyncRequests = [];

        foreach ($requests as $request) {
            if ($request instanceof AsyncRequest) {
                $asyncRequests[] = $request;
            }
        }

        $responses = $client->pool(
            fn (Pool $pool) => array_map(
                fn (AsyncRequest $request) => $pool->{$request->method}($this->baseUrl.$request->uri),
                $asyncRequests
            )
        );

        foreach ($responses as $response) {
            $this->checkResponse($response);
        }

        return $responses;
    }

    public function fromUser(User $user): self
    {
        return $this->withHeaders([
            'X-User-ID' => $user->id,
            'X-User-Type' => 'User',
            'X-User-Platform' => 'web',
        ]);
    }

    public function fromBitrixUser(User $user): self
    {
        return $this->withHeaders([
            'X-User-ID' => $user->bitrix_id,
            'X-User-Type' => 'User',
            'X-User-Platform' => 'web',
        ]);
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    private function execute(array $data = []): Response
    {
        $client = (clone $this->client)->withHeaders($this->headers);
        $client = $this->applyAttachments($client);

        $response = $client->{$this->method}($this->url, $data);

        $this->resetClient();

        $this->checkResponse($response);

        return $response;
    }

    private function applyAttachments(PendingRequest $client): PendingRequest
    {
        if (empty($this->attachments)) {
            return $client;
        }

        foreach ($this->attachments as $name => $attachment) {
            if (is_array($attachment)) {
                foreach ($attachment as $key => $item) {
                    $client->attach(
                        $name . '[' . $key . ']',
                        $item->openFile(),
                        $item->getClientOriginalName()
                    );
                }
            } else {
                $client->attach(
                    $name,
                    $attachment->openFile(),
                    $attachment->getClientOriginalName()
                );
            }
        }

        return $client;
    }

    private function resetClient(): void
    {
        $this->method = null;
        $this->url = null;
        $this->headers = [];
        $this->attachments = [];
    }

    /**
     * @throws ClientErrorException
     * @throws ServerErrorException
     */
    private function checkResponse(Response $response): void
    {
        if ($response->serverError()) {
            Log::notice(
                sprintf(
                    '%s: %d status. Body: %s',
                    get_class($this),
                    $response->status(),
                    $response->body()
                )
            );

            throw new ServerErrorException(
                $response->json('message'),
                $response->status()
            );
        }

        if ($response->clientError() && $this->failOnClientErrors) {
            Log::notice(
                sprintf(
                    '%s: %d status. Body: %s',
                    get_class($this),
                    $response->status(),
                    $response->body()
                )
            );

            throw new ClientErrorException(
                $response->json('message'),
                $response->status()
            );
        }
    }
}