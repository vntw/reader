<?php

namespace Reader\Controller;

use Reader\Tag\Tree;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AboutController implements ControllerProviderInterface
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

		$router->get('/about', function (Request $request) use ($app) {

			if ($app['app.pjax']->hasHeader($request)) {
				$data = $app['twig']->render('blocks/about.inc.html.twig');
			} else {
				$tree = new Tree($app['orm.em']);

				$data = $app['twig']->render('about.html.twig', array(
					'tagTree' => $tree->build()
				));
			}

			return $data;
		});

		return $router;
	}
}
