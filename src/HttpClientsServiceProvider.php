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
            function () {
                return app()->isProduction() 
                    ? new SmsGatewayClient(
                        config('http_clients.sms.base_url'),
                        config('http_clients.sms.token')
                    ) 
                    : new SmsDevClient;
            }
        );
    }

    public function boot()
    {
        //
    }
}