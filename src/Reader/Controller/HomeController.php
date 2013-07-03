<?php

namespace Reader\Controller;

use Reader\Tag\Tree;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController implements ControllerProviderInterface
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

		$router->match('/', array($this, 'createView'))->bind('home');
		$router->match('/locale/{locale}', array($this, 'changeLocale'))->bind('change_locale_route');

		return $router;
	}

	public function createView(Request $request, Application $app)
	{
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$tagTree = new Tree($entityManager);

		$qb = $entityManager->createQueryBuilder();
		$items = $qb->select('i')
			->from('Reader\\Entity\\Item', 'i')
			->where('i.date > ?1')
			->orderBy('i.date', 'DESC')
			->setMaxResults(30)
			->setParameter(1, new \DateTime('-6 days'));

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/home.inc.html.twig', array(
				'items' => $items->getQuery()->getResult()
			));
		}

		return $app['twig']->render('home.html.twig', array(
			'tagTree' => $tagTree->build(),
			'items' => $items->getQuery()->getResult()
		));
	}


	/**
	 * @param  Application $app
	 * @param  Request     $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function changeLocale(Application $app, Request $request)
	{
		$locale = $request->attributes->get('locale');

		if (!in_array($locale, array('de_DE', 'en_GB'))) {
			$app->abort(404, 'No locale found.');
		}

		$app['session']->set('session.locale', $locale);

		return $app->redirect('/');
	}

}
