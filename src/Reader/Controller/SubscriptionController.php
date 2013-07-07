<?php

namespace Reader\Controller;

use Reader\Entity\Subscription;
use Reader\Item\ItemList;
use Reader\DataCollector\DataCollectorInterface;
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

		$router->match('/s/{name}/{id}', array($this, 'createSubscriptionView'))->bind('subscription_view');

		$router->get('/s/add', function (Request $request) use ($app) {
			if (!$request->isXmlHttpRequest()) {
				$app->abort(400, 'LOL');
			}

			$url = null;
			$feedUrl = null;

			$subscription = new Subscription();
			$subscription->setName('Test')
				->setType(DataCollectorInterface::TYPE_RSS)
				->setUrl($url)
				->setFeedUrl($feedUrl);
		});

		return $router;
	}

	public function createSubscriptionView(Request $request, Application $app)
	{
		$subscriptionId = (int) $request->attributes->get('id');
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$list = new ItemList($entityManager, ItemList::TYPE_SUBSCRIPTION, $subscriptionId);

		if ($app['app.pjax']->hasHeader($request)) {
			return $app['twig']->render('blocks/element/generic_list.html.twig', array(
				'title' => 'dsfsdfsf',
				'items' => $list->getItems(30)
			));
		}

		return $app['twig']->render('generic_list.html.twig', array(
			'title' => 'dsfsdfsf',
			'items' => $list->getItems(30)
		));
	}

}
