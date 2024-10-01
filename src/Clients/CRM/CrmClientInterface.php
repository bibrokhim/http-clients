<?php

namespace Bibrokhim\HttpClients\Clients\CRM;

interface CrmClientInterface
{
    public function professions(): array;

    public function regions(): array;

    public function cities(string $regionId): array;

    public function customer(int $customerId): array;

    public function getCustomerId(int $masterId): int;
}