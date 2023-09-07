<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

interface SmsClientInterface
{
    public function send(string $phoneNumber, string $message): void;
}