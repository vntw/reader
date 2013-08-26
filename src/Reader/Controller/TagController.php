<?php

namespace Reader\Controller;

use Reader\Entity\Tag;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class TagController implements ControllerProviderInterface
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

		$router->match('/t/{func}/{action}/{id}', array($this, 'changeTag'))
			->bind('change_tag')
			->assert('func', 'read');

        $router->get('/t/add', function (Request $request) use ($app) {
            if (!$request->isXmlHttpRequest()) {
                $app->abort(400, 'LOL');
            }

            // TODO add tag
        });

        return $router;
    }

	/**
	 * @param  Request      $request
	 * @param  Application  $app
	 * @return JsonResponse
	 */
	public function changeTag(Request $request, Application $app)
	{
		$func = $request->attributes->get('func');
		$add = $request->attributes->get('action');
		$entityManager = $app['orm.em'];

		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$tagId = (int) $request->attributes->get('id');

		$tag = $entityManager->getRepository('Reader\\Entity\\Tag')->find($tagId);

		if (!$tag instanceof Tag) {
			return new JsonResponse(array('error' => 'Invalid tag.'));
		}

		switch ($func) {
			case 'read':

				//"UPDATE item AS i JOIN subscription AS s ON s.id = i.subscriptionId JOIN tag AS t ON s.tagId = i.id SET read=1";

				$tag->setRead($add);
				break;
		}

		$entityManager->persist($tag);
		$entityManager->flush();

		return new JsonResponse(array('test' => 'dsfds'));
	}

}
