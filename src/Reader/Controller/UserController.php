<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ven
 * Date: 04.07.13
 * Time: 23:08
 * To change this template use File | Settings | File Templates.
 */

namespace Reader\Controller;

use Reader\Entity\Item;
use Reader\Item\ItemList;
use Silex\ControllerCollection;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

		$router->match('/favs', array($this, 'createFavsView'));
		$router->match('/saved', array($this, 'createSavedView'));

		return $router;
	}

	public function createFavsView(Request $request, Application $app)
	{
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$list = new ItemList($entityManager, ItemList::TYPE_FAVOURITES);

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/element/generic_list.html.twig', array(
				'title' => 'title_favourites',
				'items' => $list->getItems(30)
			));
		}

		return $app['twig']->render('generic_list.html.twig', array(
			'title' => 'title_favourites',
			'items' => $list->getItems(30)
		));
	}

	public function createSavedView(Request $request, Application $app)
	{
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$list = new ItemList($entityManager, ItemList::TYPE_SAVED);

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/element/generic_list.html.twig', array(
				'title' => 'title_saved',
				'items' => $list->getItems(30)
			));
		}

		return $app['twig']->render('generic_list.html.twig', array(
			'title' => 'title_saved',
			'items' => $list->getItems(30)
		));
	}

}
