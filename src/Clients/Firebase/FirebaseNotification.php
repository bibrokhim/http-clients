<?php

namespace Bibrokhim\HttpClients\Clients\Firebase;

class FirebaseNotification
{
    public function __construct(
        public string $title,
        public string $body,
        public ?string $image = null,
    )
    {
    }

    public function toArray(): array
    {
        $arr = [
            'title' => $this->title,
            'body' => $this->body,
        ];

        if (isset($this->image)) {
            $arr = array_merge($arr, ['image' => $this->image]);
        }

        return $arr;
    }
}