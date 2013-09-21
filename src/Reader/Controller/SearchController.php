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

        $router->match('/search', array($this, 'searchByQuery'))
            ->bind('search');

        return $router;
    }

    public function searchByQuery(Request $request, Application $app)
    {
        $query = trim($request->get('query'));

        $data = $app['twig']->render('search.html.twig', array(
            'query' => $query
        ));

        if ($app['app.pjax']->hasHeader($request)) {
            return new Response($data);
        }

        return $data;
    }

}
