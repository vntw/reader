<?php

require_once __DIR__ . '/app.php';

use Reader\Console\Application;
use Symfony\Component\Console\Input\InputOption;

$console = new Application($app);
$console->run();
