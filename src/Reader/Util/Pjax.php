<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ven
 * Date: 18.06.13
 * Time: 22:20
 * To change this template use File | Settings | File Templates.
 */

namespace Reader\Util;

use Symfony\Component\HttpFoundation\Request;

class Pjax
{

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function hasHeader(Request $request)
	{
		return $request->server->has('HTTP_X_PJAX');
	}
}
