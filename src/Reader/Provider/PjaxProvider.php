<?php

namespace Reader\Provider;

use Reader\Util\Pjax;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PjaxProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['app.pjax'] = $app->share(function () {
            return new Pjax();
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registers
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
