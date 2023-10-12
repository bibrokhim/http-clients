<?php

namespace Bibrokhim\HttpClients\Clients\CRM;

use App\Models\Device;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class CrmCacheClient implements CrmClientInterface
{
    public function __construct(
        private string $crmBaseUrl
    )
    {
    }

    public function professions(): array
    {
        if (Cache::has('crm.professions'))
            return Cache::get('crm.professions');

        $client = new CrmClient($this->crmBaseUrl);
        $data = $client->professions();

        return $this->cache('crm.professions', $data);
    }

    public function regions(): array
    {
        if (Cache::has('crm.regions'))
            return Cache::get('crm.regions');

        $client = new CrmClient($this->crmBaseUrl);
        $data = $client->regions();

        return $this->cache('crm.regions', $data);
    }

    public function cities(string $regionId): array
    {
        if (Cache::has("crm.region.$regionId.cities"))
            return Cache::get("crm.region.$regionId.cities");

        $client = new CrmClient($this->crmBaseUrl);
        $data = $client->cities($regionId);

        return $this->cache("crm.region.$regionId.cities", $data);
    }

    private function cache(string $key, array $data): array
    {
        $lock = Cache::lock($key, 10);

        if ($lock->get()) {
            Cache::put($key, $data, 24 * 3600);

            $lock->release();
        }

        return $data;
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