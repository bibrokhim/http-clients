<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

use Bibrokhim\HttpClients\Clients\BaseClient;
use Illuminate\Support\Facades\Http;

class SmsGatewayClient extends BaseClient implements SmsClientInterface
{
    public function __construct(string $baseUrl, string $token)
    {
        $this->client = Http::baseUrl($baseUrl)
            ->withToken($token);
    }

    public function send(string $phoneNumber, string $message): void
    {
        $this->post('/send', compact('phoneNumber', 'message'));
    }
}