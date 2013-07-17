<?php

namespace Reader\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController implements ControllerProviderInterface
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

        $router->post('/search', function (Request $request) use ($app) {
            $data = $app['twig']->render('search.html.twig', array(
                'query' => htmlspecialchars(trim($request->get('query')))
            ));

            if ($request->isXmlHttpRequest()) {
                return new Response($data);
            }

            return $data;
        });

        $router->get('/search', function (Request $request) use ($app) {
            $data = $app['twig']->render('search.html.twig', array(
                'query' => htmlspecialchars(trim($request->get('query')))
            ));

            if ($request->isXmlHttpRequest()) {
                return new Response($data);
            }

            return $data;
        });

        return $router;
    }
}
