<?php

namespace Bibrokhim\HttpClients;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function store(string $key, array $data, int $seconds): array
    {
        $lock = Cache::lock($key, 10);

        if ($lock->get()) {
            Cache::put($key, $data, $seconds);

            $lock->release();
        }

        return $data;
    }
}