<?php

namespace Bibrokhim\HttpClients;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function store(string $key, array|string $data, int $seconds): array|string
    {
        $lock = Cache::lock($key, 10);

        if ($lock->get()) {
            Cache::put($key, $data, $seconds);

            $lock->release();
        }

        return $data;
    }
}