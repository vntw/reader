<?php

namespace Reader\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $router = $app['controllers_factory'];
        /* @var $router Application */

        $router->get('/subscription', function (Request $request) use ($app) {
            return $app['twig']->render('about.html.twig');
        });

        $router->get('/subscription/add', function (Request $request) use ($app) {
            if (!$request->isXmlHttpRequest()) {
                $app->abort(400, 'LOL');
            }
        });

        return $router;
    }
}
