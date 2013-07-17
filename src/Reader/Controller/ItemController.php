<?php

namespace Reader\Controller;

use Reader\Entity\Item;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ItemController implements ControllerProviderInterface
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

        $router->match('/i/{func}/{action}/{id}', array($this, 'changeItem'))
            ->bind('change_item')
            ->assert('func', 'favs|saved|read')
            ->assert('action', 'add|del');

        return $router;
    }

    /**
     * @param  Request      $request
     * @param  Application  $app
     * @return JsonResponse
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
            return new JsonResponse(array('error' => 'Invalid item.'));
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

        return new JsonResponse(array('test' => 'dsfds'));
    }

}
