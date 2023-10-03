<?php

namespace Bibrokhim\HttpClients\Clients\Firebase;

use Bibrokhim\HttpClients\Clients\BaseClient;

class FirebaseClient extends BaseClient implements FirebaseClientInterface
{
    readonly private string $appName;

    public function __construct(string $baseUrl, string $token, string $appName)
    {
        parent::__construct($baseUrl, $token);

        $this->appName = $appName;
    }

    public function sendToToken(FirebaseNotification $notification, string $token): void
    {
        $this->post(
            "/fcm/app/$this->appName/send",
            array_merge($notification->toArray(), compact('token'))
        );
    }

    public function sendMulticast(FirebaseNotification $notification, array $tokens): void
    {
        $this->post(
            "/fcm/app/$this->appName/send-multicast",
            array_merge($notification->toArray(), compact('tokens'))
        );
    }

    public function sendToTopic(FirebaseNotification $notification, string $topic): void
    {
        $this->post(
            "/fcm/app/$this->appName/send-to-topic",
            array_merge($notification->toArray(), compact('topic'))
        );
    }

    public function subscribeToTopics(array $tokens, array $topics): void
    {
        $this->post(
            "/fcm/app/$this->appName/subscribe",
            compact('tokens', 'topics')
        );
    }

    public function unsubscribeFromTopics(array $tokens, array $topics): void
    {
        $this->post(
            "/fcm/app/$this->appName/unsubscribe",
            compact('tokens', 'topics')
        );
    }

    public function unsubscribeFromAllTopics(array $tokens): void
    {
        $this->post(
            "/fcm/app/$this->appName/unsubscribe-from-all",
            compact('tokens')
        );
    }
}