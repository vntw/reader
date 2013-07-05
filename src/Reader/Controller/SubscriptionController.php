<?php

namespace Reader\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController implements ControllerProviderInterface
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

		$router->get('/subscription', function (Request $request) use ($app) {
			return $app['twig']->render('about.html.twig');
		});

		$router->get('/subscription/add', function (Request $request) use ($app) {
			if (!$request->isXmlHttpRequest()) {
				$app->abort(400, 'LOL');
			}
		});

//		$router->get('/list/{id}', function (Request $request) use ($app) {
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
