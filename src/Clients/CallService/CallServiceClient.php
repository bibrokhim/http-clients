<?php

namespace Bibrokhim\HttpClients\Clients\CallService;

use Bibrokhim\HttpClients\Clients\BaseClient;

class CallServiceClient extends BaseClient
{
    public const API_CLIENT_EPA_CRM = 'epa-crm';

    public const API_CLIENT_SERVICE_CRM = 'service-crm';

    /**
     * call-service scopes every request to the call centre named in X-Api-Client.
     */
    public function forApiClient(string $apiClient): self
    {
        return $this->withHeaders(['X-Api-Client' => $apiClient]);
    }
}
