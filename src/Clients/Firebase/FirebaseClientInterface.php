<?php

namespace Bibrokhim\HttpClients\Clients\Firebase;

interface FirebaseClientInterface
{
    public function sendToToken(FirebaseNotification $notification, string $token): void;

    public function sendMulticast(FirebaseNotification $notification, array $tokens): void;

    public function sendToTopic(FirebaseNotification $notification, string $topic): void;

    public function subscribeToTopics(array $tokens, array $topics): void;

    public function unsubscribeFromTopics(array $tokens, array $topics): void;

    public function unsubscribeFromAllTopics(array $tokens): void;
}