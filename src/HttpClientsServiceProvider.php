<?php

namespace Bibrokhim\HttpClients;

use Bibrokhim\HttpClients\Clients\CrmClient;
use Bibrokhim\HttpClients\Clients\MediaClient;
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
            fn() => app()->isProduction() 
                ? new SmsGatewayClient(
                    config('http_clients.sms.base_url'),
                    config('http_clients.sms.token')
                ) 
                : new SmsDevClient
        );

        $this->app->bind(MediaClient::class, function () {
            return new MediaClient(
                config('http_clients.media.base_url'),
                config('http_clients.media.token')
            );
        });

        $this->app->bind(CrmClient::class, function () {
            return new CrmClient(
                config('http_clients.crm.base_url'),
            );
        });
    }

    public function boot()
    {
        //
    }
}