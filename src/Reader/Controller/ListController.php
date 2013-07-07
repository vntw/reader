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

		$router->match('/l/{type}', array($this, 'getList'))->bind('get_list');

		return $router;
	}

	public function getList(Application $app, Request $request)
	{
		$entityManager = $app['orm.em'];

		$type = $request->attributes->get('type');
		$typeId = (int) $request->get('type-id') ? : null;
		$lastId = (int) $request->get('last-id') ? : null;
		$itemAmount = (int) $request->get('amount') ? : 5;
		$sort = $request->get('sort') === 'asc' ? 'asc' : 'desc';
		$format = $request->get('format');

		$list = new ItemList($entityManager);
		$list->setType($type)
			->setTypeId($typeId)
			->setItemAmount($itemAmount)
			->setSort($sort)
			->setLastId($lastId);

		$items = array();

		foreach ($list->getItems() as $item) {
			/* @var Item $item */
			$items[] = $item;
		}

		switch ($format) {
			case 'view':
				if ($app['app.pjax']->hasHeader($request)) {
					return $app['twig']->render('blocks/element/generic_list.html.twig', array(
						'title' => $type,
						'items' => $items
					));
				}

				return $app['twig']->render('generic_list.html.twig', array(
					'title' => $type,
					'items' => $items
				));

				break;
			case 'json':
			default:
				return new JsonResponse(array('data' => $items));
				break;
		}
	}

}
