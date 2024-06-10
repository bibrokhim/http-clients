<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

use Bibrokhim\HttpClients\Clients\BaseClient;

class SmsGatewayClient extends BaseClient implements SmsClientInterface
{
    public function send(string $phoneNumber, string $message): void
    {
        $this->post('/send', compact('phoneNumber', 'message'));
    }

    public function sendMany(array $phoneNumbers, string $message): void
    {
        $this->post('/send-many', compact('phoneNumbers', 'message'));
    }
}