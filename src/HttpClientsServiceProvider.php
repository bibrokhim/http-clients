<?php

namespace Bibrokhim\HttpClients;

use Bibrokhim\HttpClients\Clients\CRM\CrmCacheClient;
use Bibrokhim\HttpClients\Clients\CRM\CrmClient;
use Bibrokhim\HttpClients\Clients\CRM\CrmClientInterface;
use Bibrokhim\HttpClients\Clients\Firebase\FirebaseClient;
use Bibrokhim\HttpClients\Clients\Firebase\FirebaseClientInterface;
use Bibrokhim\HttpClients\Clients\Firebase\FirebaseDevClient;
use Bibrokhim\HttpClients\Clients\Helpdesk\HelpdeskClient;
use Bibrokhim\HttpClients\Clients\Media\MediaClient;
use Bibrokhim\HttpClients\Clients\Products\ProductsClient;
use Bibrokhim\HttpClients\Clients\SMS\SmsClientInterface;
use Bibrokhim\HttpClients\Clients\SMS\SmsDevClient;
use Bibrokhim\HttpClients\Clients\SMS\SmsGatewayClient;
use Illuminate\Support\ServiceProvider;

class HttpClientsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'http_clients');

        $this->app->bind(SmsClientInterface::class, fn() =>
            app()->isProduction()
                ? new SmsGatewayClient(config('http_clients.sms.base_url'), config('http_clients.sms.token'))
                : new SmsDevClient()
        );

        $this->app->bind(FirebaseClientInterface::class, fn() =>
            app()->isProduction()
                ? new FirebaseClient(config('http_clients.firebase.base_url'), config('http_clients.firebase.token'), 'epa-usta')
                : new FirebaseDevClient()
        );

        $this->app->bind(MediaClient::class, function () {
            return new MediaClient(
                config('http_clients.media.base_url'),
                config('http_clients.media.token')
            );
        });

        $this->app->bind(CrmClientInterface::class, function () {
            return config('http_clients.cache')
                ? new CrmCacheClient(config('http_clients.crm.base_url'))
                : new CrmClient(config('http_clients.crm.base_url'));

        });

        $this->app->bind(HelpdeskClient::class, function () {
            return new HelpdeskClient(
                config('http_clients.helpdesk.base_url'),
            );
        });

        $this->app->bind(ProductsClient::class, function () {
            return new ProductsClient(
                config('http_clients.products.base_url'),
            );
        });
    }

    public function boot()
    {
        //
    }
}