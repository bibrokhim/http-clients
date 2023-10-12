<?php

namespace Bibrokhim\HttpClients\Clients\CRM;

use App\Models\Device;
use Bibrokhim\HttpClients\CacheHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class CrmCacheClient extends CrmClient
{
    public function professions(): array
    {
        if (Cache::has('crm.professions'))
            return Cache::get('crm.professions');

        $data = parent::professions();

        return CacheHelper::store('crm.professions', $data, 24 * 3600);
    }

    public function regions(): array
    {
        if (Cache::has('crm.regions'))
            return Cache::get('crm.regions');

        $data = parent::regions();

        return CacheHelper::store('crm.regions', $data, 24 * 3600);
    }

    public function cities(string $regionId): array
    {
        if (Cache::has("crm.region.$regionId.cities"))
            return Cache::get("crm.region.$regionId.cities");

        $data = parent::cities($regionId);

        return CacheHelper::store("crm.region.$regionId.cities", $data, 24 * 3600);
    }
}