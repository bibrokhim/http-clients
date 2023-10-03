<?php

namespace Bibrokhim\HttpClients\Clients\Firebase;

use Illuminate\Support\Facades\Http;

class FirebaseDevClient implements FirebaseClientInterface
{
    private string $uri;

    public function __construct()
    {
        $this->uri = 'https://api.telegram.org/bot' . config('http_clients.sms.telegram_bot_token') . '/sendMessage';
    }

    public function sendToToken(FirebaseNotification $notification, string $token): void
    {
        $message =
            "*$notification->title*" . PHP_EOL
            . $notification->body . PHP_EOL
            . "_Token:_ `$token`";

        $this->send($message);
    }

    public function sendMulticast(FirebaseNotification $notification, array $tokens): void
    {
        $message =
            "*$notification->title*" . PHP_EOL
            . $notification->body . PHP_EOL
            . '_Multicast:_ `' . implode(' | ', $tokens) . '`';

        $this->send($message);
    }

    public function sendToTopic(FirebaseNotification $notification, string $topic): void
    {
        $message =
            "*$notification->title*" . PHP_EOL
            . $notification->body . PHP_EOL
            . "_Topic:_ `$topic`";

        $this->send($message);
    }

    public function subscribeToTopics(array $tokens, array $topics): void
    {
        $message =
            '_Subscribe to topics:_ `' . implode(', ', $topics) . '`' . PHP_EOL
            . '_Tokens:_ `' . implode(' | ', $tokens) . '`';

        $this->send($message);
    }

    public function unsubscribeFromTopics(array $tokens, array $topics): void
    {
        $message =
            '_Unsubscribe from topics:_ `' . implode(', ', $topics) . '`' . PHP_EOL
            . '_Tokens:_ `' . implode(' | ', $tokens) . '`';

        $this->send($message);
    }

    public function unsubscribeFromAllTopics(array $tokens): void
    {
        $message =
            '_Unsubscribe from all topics_' . PHP_EOL
            . '_Tokens:_ `' . implode(' | ', $tokens) . '`';

        $this->send($message);
    }

    private function send(string $message): void
    {
        foreach (explode(',', config('http_clients.sms.telegram_chats')) as $chatId) {
            Http::get($this->uri, [
                'chat_id' => trim($chatId),
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        }
    }
}