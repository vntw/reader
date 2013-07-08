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

//		$router->match('/favs', array($this, 'createFavsView'));
//		$router->match('/saved', array($this, 'createSavedView'));

		return $router;
	}

}
