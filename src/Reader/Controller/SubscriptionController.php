<?php

namespace Reader\Controller;

use Reader\Entity\Subscription;
use Reader\DataCollector\DataCollectorInterface;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $router->match('/s/{func}/{action}/{id}', array($this, 'changeSubscription'))
            ->bind('change_subscription')
            ->assert('func', 'markread');

        $router->match('/s/addform', array($this, 'getAddForm'))
            ->bind('add_subscription_form');

        return $router;
    }

    /**
     * @param  Request     $request
     * @param  Application $app
     * @return JsonResponse
     */
    public function changeSubscription(Request $request, Application $app)
    {
        $func = $request->attributes->get('func');
        $add = $request->attributes->get('action');
        $entityManager = $app['orm.em'];

        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $subId = (int) $request->attributes->get('id');

        $subscription = $entityManager->getRepository('Reader\\Entity\\Subscription')->find($subId);

        if (!$subscription instanceof Subscription) {
            return new JsonResponse(array('error' => 'Invalid Sub.'));
        }

        switch ($func) {
            case 'markread':

                //"UPDATE item AS i JOIN subscription AS s ON s.id = i.subscriptionId JOIN tag AS t ON s.tagId = i.id SET read=1";

                $subscription->markItemsRead($entityManager);
                break;
        }

        $entityManager->persist($subscription);
        $entityManager->flush();

        return new JsonResponse(array('test' => 'dsfds'));
    }

    public function getAddForm(Request $request, Application $app)
    {
        $url = null;
        $feedUrl = null;

        $subscription = new Subscription();
        $subscription->setName('Test')
            ->setType(DataCollectorInterface::TYPE_RSS)
            ->setUrl($url)
            ->setFeedUrl($feedUrl);
    }

}
