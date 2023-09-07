<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

use Bibrokhim\HttpClients\Clients\BaseClient;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SmsGatewayClient extends BaseClient implements SmsClientInterface
{
    public function __construct()
    {
        $this->client = Http::baseUrl(config('http_clients.sms.base_url'))
            ->withToken(config('microservices.sms.token'));
    }

    public function send(string $phoneNumber, string $message): void
    {
        $this->post('/send', compact('phoneNumber', 'message'));
    }
}