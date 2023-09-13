<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsDevClient implements SmsClientInterface
{
    public function send(string $phoneNumber, string $message): void
    {
        $url = 'https://api.telegram.org/bot' . config('http_clients.sms.telegram_bot_token') . '/sendMessage';
        Log::info("$phoneNumber: $message");

        if (! app()->environment('testing')) {
            Http::get($url, [
                'chat_id' => 944751850,
                'text' => "$phoneNumber: $message"
            ]);

            Http::get($url, [
                'chat_id' => 2707871,
                'text' => "$phoneNumber: $message"
            ]);
        }
    }
}