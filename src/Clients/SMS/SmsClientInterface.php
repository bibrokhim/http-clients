<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

interface SmsClientInterface
{
    public function send(string $phoneNumber, string $message): void;

    public function sendWhatsapp(string $phoneNumber, string $code): void;

    public function sendTelegram(string $phoneNumber, string $code): void;

    public function sendMany(array $phoneNumbers, string $message): void;
}