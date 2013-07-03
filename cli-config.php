<?php
// cli-config.php
require_once 'src/app.php';

$entityManager = $app['orm.em'];

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));
