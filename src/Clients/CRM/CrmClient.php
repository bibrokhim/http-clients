<?php

namespace Bibrokhim\HttpClients\Clients\CRM;

use App\Models\Device;
use Bibrokhim\HttpClients\Clients\BaseClient;
use Illuminate\Http\UploadedFile;

class CrmClient extends BaseClient implements CrmClientInterface
{
    public function professions(): array
    {
        return $this->get('v1/professions')->json();
    }

    public function regions(): array
    {
        return $this->get('v1/regions')->json();
    }

    public function cities(string $regionId): array
    {
        return $this->get("v1/regions/$regionId/cities")->json();
    }

    public function fromEpaUstaDevice(Device $device): self
    {
        return $this->withHeaders([
            'X-User-ID' => $device->user_id,
            'X-User-Type' => 'Master',
            'X-User-Platform' => $device->platform,
            'X-Build' => request()->header('X-Build'),
            'X-Version' => request()->header('X-Version'),
        ]);
    }

    public function customer(int $customerId): array
    {
        return $this->get("v1/customers/$customerId")->json();
    }
}