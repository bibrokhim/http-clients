<?php

namespace Bibrokhim\HttpClients\Clients\ServiceCRM;

use App\Models\Device;
use Bibrokhim\HttpClients\Clients\BaseClient;
use Illuminate\Http\Client\Response;

class ServiceCrmClient extends BaseClient implements ServiceCrmClientInterface
{
    public function serviceCountry(int $serviceId): array
    {
        return $this->serviceCountryResponse($serviceId)->json();
    }

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

    public function fromServiceTablet(Device $device): self
    {
        return $this->withHeaders([
            'X-User-ID' => $device->user_id,
            'X-User-Type' => 'Service',
            'X-User-Platform' => $device->platform,
            'X-Build' => request()->header('X-Build'),
            'X-Version' => request()->header('X-Version'),
        ]);
    }

    protected function serviceCountryResponse(int $serviceId): Response
    {
        return $this->get("v1/services/$serviceId/country");
    }
}
