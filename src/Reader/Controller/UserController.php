<?php

namespace Reader\Controller;

use Silex\ControllerCollection;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController implements ControllerProviderInterface
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

        $router->match('/settings', array($this, 'createSettingsView'))->bind('settings_view');

        return $router;
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return mixed
     */
    public function createSettingsView(Application $app, Request $request)
    {
        if ($app['app.pjax']->hasHeader($request)) {
            $data = $app['twig']->render('blocks/settings.inc.html.twig');
        } else {
            $data = $app['twig']->render('settings.html.twig');
        }

        return $data;
    }

}
