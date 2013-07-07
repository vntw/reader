<?php

namespace Reader\Controller;

use Reader\Item\ItemList;
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

		$router->match('/t/{name}/{id}', array($this, 'createTagView'))->bind('tag_view');

		$router->get('/t/add', function (Request $request) use ($app) {
			if (!$request->isXmlHttpRequest()) {
				$app->abort(400, 'LOL');
			}

			// TODO discover url
		});

		return $router;
	}

	public function createTagView(Request $request, Application $app)
	{
		$tagId = (int) $request->attributes->get('id');
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$list = new ItemList($entityManager, ItemList::TYPE_TAG, $tagId);

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/element/generic_list.html.twig', array(
				'title' => 'tag',
				'items' => $list->getItems(30)
			));
		}

		return $app['twig']->render('generic_list.html.twig', array(
			'title' => 'tag',
			'items' => $list->getItems(30)
		));
	}

}
