<?php

namespace Bibrokhim\HttpClients\Clients\ServiceCRM;

interface ServiceCrmClientInterface
{
    public function serviceCountry(int $serviceId): array;
}
