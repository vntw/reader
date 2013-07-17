<?php

namespace Reader\Controller;

use Silex\Application;
use Reader\Entity\Subscription;
use Reader\DataCollector\Factory;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CollectController implements ControllerProviderInterface
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

        $router->get('/collect', array($this, 'collect'));
        $router->get('/collect/{id}', array($this, 'collectId'))
                ->value('id', '')
                ->convert('id', function ($id) {
                            if ('' !== $id) {
                                return (int) $id;
                            }

                            return $id;
                        });

        return $router;
    }

    public function collect(Request $request, Application $app)
    {
        $entityManager = $app['orm.em'];
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $results = array();

        $subscriptionsQuery = $entityManager->getRepository('Reader\\Entity\\Subscription')->createQueryBuilder('s');
        $subscriptionsQuery->orderBy('s.name', 'ASC');
        $subscriptions = $subscriptionsQuery->getQuery()->getResult();

        foreach ($subscriptions as $subscription) {
            $results[$subscription->getId()] = $subscription;
        }

        if ($app['app.pjax']->hasHeader($request)) {
            return $app['twig']->render('blocks/collect_overview.inc.html.twig', array('subscriptions' => $results));
        } else {
            return $app['twig']->render('collect/collect_overview.html.twig', array('subscriptions' => $results));
        }
    }

    public function collectId(Request $request, Application $app, $id)
    {
        $entityManager = $app['orm.em'];
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        if (0 === $id) {
            $app->abort(404, 'invalid sub id: ' . $id);
        }

        if ($id > 0) {
            $sub = $entityManager->getRepository('Reader\\Entity\\Subscription')->find($id);

            if (!$sub instanceof Subscription) {
                $app->abort(404, 'no sub found with id: ' . $id);
            }

            $subscriptions = array($sub);
        } else {
            $subscriptions = $entityManager->getRepository('Reader\\Entity\\Subscription')->findAll();
        }

        $results = array();

        foreach ($subscriptions as $subscription) {
            $collector = Factory::fromSubscription($subscription, $app);
            $result = $collector->collect();

            $results[$subscription->getId()] = $result;
            $results[$subscription->getId()]['subscription'] = $subscription->toArray();
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array_values($results));
        }

        $data = $app['twig']->render('collect.html.twig', array('results' => $results));

        return $data;
    }

}
