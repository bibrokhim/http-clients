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

            foreach (explode(',', config('http_clients.sms.telegram_chats')) as $chatId) {
                Http::get($url, [
                    'chat_id' => trim($chatId),
                    'text' => "$phoneNumber: $message"
                ]);
            }

        }
    }

    public function sendMany(array $phoneNumbers, string $message): void
    {
        $url = 'https://api.telegram.org/bot' . config('http_clients.sms.telegram_bot_token') . '/sendMessage';
        $count = count($phoneNumbers);
        $infoText = "SMS pack ($count): $message - ".implode(',',$phoneNumbers);

        Log::info($infoText);

        if (! app()->environment('testing')) {

            foreach (explode(',', config('http_clients.sms.telegram_chats')) as $chatId) {
                Http::get($url, [
                    'chat_id' => trim($chatId),
                    'text' => $infoText
                ]);
            }

        }
    }
}