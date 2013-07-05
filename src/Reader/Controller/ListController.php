<?php

namespace Reader\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
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


//		$router->get('/list/{type}/{action}/{id}', function (Request $request) use ($app) {
//			$entityManager = $app['orm.em'];
//
//			/* @var $entityManager \Doctrine\ORM\EntityManager */
//
//			$qb = $entityManager->createQueryBuilder();
//			$items = $qb->select('i')
//				->from('Reader\\Entity\\Item', 'i')
////			->where('i.date > ?1')
//				->where('i.subscriptionId = ?1')
//				->orderBy('i.date', 'DESC')
//				->setMaxResults(30);
//			->setParameter(1, $request->attributes->get(''));
//
//			if ($app['app.pjax']->hasHeader($request)) {
//				return $app['twig']->render('blocks/favs.inc.html.twig', array(
//					'items' => $items->getQuery()->getResult()
//				));
//			}
//
//			return $app['twig']->render('favs.html.twig', array(
//				'items' => $items->getQuery()->getResult()
//			));
//		});

		return $router;
	}
}
