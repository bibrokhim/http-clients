<?php

namespace Bibrokhim\HttpClients\Clients;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseClient
{
    protected ?string $method;
    protected ?string $url;
    protected array $headers = [
        'Accept' => 'application/json',
    ];
    protected array $attachments = [];
    protected PendingRequest $client;

    public function __construct(string $baseUrl, ?string $token = null)
    {
        $this->client = Http::baseUrl($baseUrl);
        
        if (isset($token)) {
            $this->client->withToken($token);
        }

        $this->headers = array_merge(
            $this->headers,
            ['Accept-Language' => app()->getLocale()]
        );
    }

    public function get(string $url): Response
    {
        $this->url = $url;
        $this->method = 'get';

        return $this->execute();
    }

    public function post(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'post';

        return $this->execute($data);
    }

    public function patch(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'patch';

        return $this->execute($data);
    }

    public function put(string $url, array $data = []): Response
    {
        $this->url = $url;
        $this->method = 'put';

        return $this->execute($data);
    }

    public function delete(string $url): Response
    {
        $this->url = $url;
        $this->method = 'delete';

        return $this->execute();
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

    private function execute(array $data = []): Response
    {
        $client = (clone $this->client)->withHeaders($this->headers);
        $client = $this->applyAttachments($client);

        $response = $client->{$this->method}($this->url, $data);

        $this->resetClient();

        if ($response->serverError()) {
            Log::alert(
                sprintf(
                    '%s: %d status. Body: %s', 
                    get_class($this), 
                    $response->status(), 
                    $response->body()
                )
            );
            
            throw new Exception(
                $response->json('message'), 
                $response->status()
            );
        }

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
}