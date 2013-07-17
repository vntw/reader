<?php

require_once __DIR__ . '/app.php';

use Reader\Console\Application;

$console = new Application($app);
$console->run();
