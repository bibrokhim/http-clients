<?php

namespace Bibrokhim\HttpClients\Clients\Pos;

use Bibrokhim\HttpClients\Clients\BaseClient;

class PosClient extends BaseClient
{
    /**
     * Authenticate request on behalf of a POS seller.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     */
    public function fromSeller($user): self
    {
        return $this->withHeaders([
            'X-User-ID' => $user->id,
            'X-User-Type' => 'Seller',
            'X-User-Platform' => request()->header('X-User-Platform', 'web'),
            'X-Warehouse-ID' => $user->warehouse_id,
        ]);
    }

    /**
     * Authenticate request on behalf of an Admin (no warehouse_id).
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     */
    public function fromAdmin($user): self
    {
        return $this->withHeaders([
            'X-User-ID' => $user->id,
            'X-User-Type' => 'Admin',
            'X-User-Platform' => request()->header('X-User-Platform', 'web'),
        ]);
    }
}
