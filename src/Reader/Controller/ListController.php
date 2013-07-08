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

		$router->match('/{type}/{name}/{typeId}', array($this, 'fromFunc'))
			->assert('type', 's|t|saved|favs')
			->value('name', null)
			->value('typeId', null)
			->bind('list_view');

		$router->match('/l/{type}/{name}/{typeId}', array($this, 'fromList'))
			->value('name', null)
			->value('typeId', null)
			->bind('list_get');

		return $router;
	}

	/**
	 * @param Application $app
	 * @param Request     $request
	 * @return JsonResponse
	 */
	public function fromFunc(Application $app, Request $request)
	{
		$type = $this->getTypeForAlias($request->attributes->get('type'));

		if (!$type) {
			exit;
		}

		return $this->fetchList($app, $request, $type, $request->attributes->get('typeId'));
	}

	/**
	 * @param Application $app
	 * @param Request     $request
	 * @return JsonResponse
	 */
	public function fromList(Application $app, Request $request)
	{
		$type = $request->attributes->get('type');
		$typeId = $request->attributes->get('typeId') ? : (int) $request->get('type-id');
		$lastId = (int) $request->get('last-id') ? : null;
		$itemAmount = (int) $request->get('amount') ? : 5;
		$sort = $request->get('sort') === 'asc' ? 'asc' : 'desc';
		$format = $request->get('format');

		return $this->fetchList($app, $request, $type, $typeId, $lastId, $itemAmount, $sort, $format);
	}

	/**
	 * @param Application $app
	 * @param Request     $request
	 * @param string      $type
	 * @param int         $typeId
	 * @param int         $lastId
	 * @param int         $itemAmount
	 * @param int         $sort
	 * @param string      $format
	 * @return JsonResponse
	 */
	public function fetchList(Application $app, Request $request, $type, $typeId = null, $lastId = null, $itemAmount = null, $sort = null, $format = null)
	{
		$entityManager = $app['orm.em'];
		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$list = new ItemList($entityManager);
		$list->setType($type)
			->setTypeId($typeId)
			->setItemAmount($itemAmount ? : 10)
			->setSort($sort)
			->setLastId($lastId);

		$items = array();

		foreach ($list->getItems() as $item) {
			/* @var Item $item */
			$items[] = $item->toArray();
		}

		$data = array(
			'type' => $type,
			'items' => $items
		);

		if ($type === ItemList::TYPE_SUBSCRIPTION) {
			$sub = $entityManager->getRepository('Reader\\Entity\\Subscription')->find($typeId);
			$data['title'] = $sub->getName();
		}

		switch ($format) {
			case 'json':
				return new JsonResponse(array('data' => $items));
				break;
			case 'view':
			default:
				if ($app['app.pjax']->hasHeader($request)) {
					return $app['twig']->render('blocks/element/generic_list.html.twig', $data);
				}

				return $app['twig']->render('generic_list.html.twig', $data);

				break;
		}
	}

	/**
	 * @param string $alias
	 * @return mixed
	 */
	private function getTypeForAlias($alias)
	{
		$aliases = array(
			's' => ItemList::TYPE_SUBSCRIPTION,
			't' => ItemList::TYPE_TAG,
			'saved' => ItemList::TYPE_SAVED,
			'favs' => ItemList::TYPE_FAVOURITES,
		);

		return $aliases[$alias];
	}

}
