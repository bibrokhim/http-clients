<?php

namespace Bibrokhim\HttpClients;

use Bibrokhim\HttpClients\Clients\SMS\SmsClientInterface;
use Bibrokhim\HttpClients\Clients\SMS\SmsDevClient;
use Bibrokhim\HttpClients\Clients\SMS\SmsGatewayClient;
use Illuminate\Support\ServiceProvider;

class HttpClientsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'http_clients');

        $this->app->bind(
            SmsClientInterface::class,
            app()->isProduction() ? SmsGatewayClient::class : SmsDevClient::class
        );
    }

    public function boot()
    {
        //
    }
}