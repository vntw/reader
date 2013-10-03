<?php

namespace Reader\Controller;

use Reader\Subscription\Discovery\Discovery;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DiscoveryController implements ControllerProviderInterface
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

        $router->match('/discover', array($this, 'discoverUrl'));

        return $router;
    }

    /**
     * @param  Request      $request
     * @param  Application  $app
     * @return JsonResponse
     */
    public function discoverUrl(Request $request, Application $app)
    {

        if (!$request->isXmlHttpRequest()) {
            $app->abort(400);
        }

        $url = trim($request->get('url'));

        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            $app->abort(400);
        }

        $urlParts = parse_url($url);
        if (empty($urlParts['scheme'])) {
            $url = 'http://' . ltrim($url, '/');
        }

        $feeds = array();
        $error = null;
        $discovery = new Discovery($url);

        try {
            $feeds = $discovery->discover();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $result = $app['twig']->render('blocks/discover/result.html.twig', array(
            'error' => $error,
            'discovery' => $feeds
        ));

        return new JsonResponse(array(
            'html' => $result,
            'valid' => count($feeds)
        ));
    }

}
