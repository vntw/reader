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

		$router->match('/{func}/{action}/{id}', array($this, 'changeItem'))
			->assert('func', 'favs|saved|read')
			->assert('action', 'add|del');

		return $router;
	}

	public function createFavsView(Request $request, Application $app) {
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$qb = $entityManager->createQueryBuilder();
		$items = $qb->select('i')
			->from('Reader\\Entity\\Item', 'i')
//			->where('i.date > ?1')
			->where('i.favourite = 1')
			->orderBy('i.date', 'DESC')
			->setMaxResults(30);
//			->setParameter(1, new \DateTime('-6 days'));

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/favs.inc.html.twig', array(
				'items' => $items->getQuery()->getResult()
			));
		}

		return $app['twig']->render('favs.html.twig', array(
			'items' => $items->getQuery()->getResult()
		));
	}

	public function createSavedView(Request $request, Application $app) {
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$qb = $entityManager->createQueryBuilder();
		$items = $qb->select('i')
			->from('Reader\\Entity\\Item', 'i')
//			->where('i.date > ?1')
			->where('i.saved = 1')
			->orderBy('i.date', 'DESC')
			->setMaxResults(30);
//			->setParameter(1, new \DateTime('-6 days'));

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/favs.inc.html.twig', array(
				'items' => $items->getQuery()->getResult()
			));
		}

		return $app['twig']->render('favs.html.twig', array(
			'items' => $items->getQuery()->getResult()
		));
	}

	/**
	 * @param Request     $request
	 * @param Application $app
	 */
	public function changeItem(Request $request, Application $app)
	{
		$func = $request->attributes->get('func');
		$add = ('add' === $request->attributes->get('action'));
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$itemId = (int) $request->attributes->get('id');

		$item = $entityManager->getRepository('Reader\\Entity\\Item')->find($itemId);

		if (!$item instanceof Item) {
			exit;
		}

		switch ($func) {
			case 'favs':
				$item->setFavourite($add);
				break;
			case 'saved':
				$item->setSaved($add);
				break;
			case 'read':
				$item->setRead($add);
				break;
		}

		$entityManager->persist($item);
		$entityManager->flush();

		return new JsonResponse(array('test'=>'dsfds'));
	}

}
