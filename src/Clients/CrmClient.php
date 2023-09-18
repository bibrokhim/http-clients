<?php

namespace Bibrokhim\HttpClients\Clients;

use App\Models\Device;
use Illuminate\Http\UploadedFile;

class CrmClient extends BaseClient
{
    public function professions()
    {
        return $this->get('v1/professions')->json();
    }

    public function regions()
    {
        return $this->get('v1/regions')->json();
    }

    public function cities(string $id)
    {
        return $this->get("v1/regions/{$id}/cities")->json();
    }

    public function fromEpaUstaDevice(Device $device): self
    {
        return $this->withHeaders([
            'X-User-ID' => $device->user_id,
            'X-User-Type' => 'Master',
            'X-User-Platform' => $device->platform,
        ]);
    }
}