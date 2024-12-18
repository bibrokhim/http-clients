<?php

namespace Bibrokhim\HttpClients\Clients\ServiceCRM;

use App\Models\Device;
use Bibrokhim\HttpClients\Clients\BaseClient;

class ServiceCrmClient extends BaseClient
{
    public function fromServiceMobileApp(Device $device): self
    {
        return $this->withHeaders([
            'X-User-ID' => $device->user_id,
            'X-User-Type' => 'Repairer',
            'X-User-Platform' => $device->platform,
            'X-Build' => request()->header('X-Build'),
            'X-Version' => request()->header('X-Version'),
        ]);
    }

    public function fromServiceTSD(Device $device): self
    {
        return $this->withHeaders([
            'X-User-ID' => $device->user_id,
            'X-User-Type' => 'Service',
            'X-User-Platform' => $device->platform,
            'X-Build' => request()->header('X-Build'),
            'X-Version' => request()->header('X-Version'),
        ]);
    }
}