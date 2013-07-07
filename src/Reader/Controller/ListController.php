<?php

namespace Reader\Controller;

use Reader\Entity\Item;
use Reader\Item\ItemList;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ListController implements ControllerProviderInterface
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

		$router->match('/l/{type}', array($this, 'getList'))->bind('list');

		return $router;
	}

	public function getList(Application $app, Request $request)
	{
		$entityManager = $app['orm.em'];
		$list = new ItemList($entityManager, $request->attributes->get('type'));

		$items = array();
		$sort = $request->get('sort');
		$lastId = (int) $request->get('last-id');

		foreach ($list->getItems(5, $sort, $lastId) as $item) {
			/* @var Item $item */
			$items[] = $item->toArray();
		}

		return new JsonResponse(array('data' => $items));
	}

}
