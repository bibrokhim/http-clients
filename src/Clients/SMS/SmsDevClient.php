<?php

namespace Bibrokhim\HttpClients\Clients\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsDevClient implements SmsClientInterface
{
    public function send(string $phoneNumber, string $message): void
    {
        if ($phoneNumber === env('SMS_DEV_NUMBER')) {
            $smsService = new SmsService();
            $smsService->send($phoneNumber, $message);
        } else {
            Log::info("$phoneNumber: $message");

            if (! app()->environment('testing')) {
                Http::get('https://api.telegram.org/bot6135540582:AAHiXJS4tQ5eJDvogGBLps3nfA-OzSI0YFo/sendMessage', [
                    'chat_id' => 944751850,
                    'text' => "$phoneNumber: $message"
                ]);

                Http::get('https://api.telegram.org/bot6135540582:AAHiXJS4tQ5eJDvogGBLps3nfA-OzSI0YFo/sendMessage', [
                    'chat_id' => 2707871,
                    'text' => "$phoneNumber: $message"
                ]);
            }
        }
    }
}