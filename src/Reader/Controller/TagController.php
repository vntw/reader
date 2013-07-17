<?php

namespace Reader\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class TagController implements ControllerProviderInterface
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

        $router->get('/t/add', function (Request $request) use ($app) {
            if (!$request->isXmlHttpRequest()) {
                $app->abort(400, 'LOL');
            }

            // TODO add tag
        });

        return $router;
    }

}
