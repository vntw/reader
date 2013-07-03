<?php

require_once 'src/app.php';

$entityManager = $app['orm.em'];
/* @var $entityManager \Doctrine\ORM\EntityManager */

$parser = new \Reader\Importer\OPML\Parser($entityManager, new \SplFileInfo(__DIR__ . '/res/subscriptions.xml'));

try {
	$parser->parse();
} catch (Exception $e) {
	var_dump($parser->getResult()->toArray());
}
