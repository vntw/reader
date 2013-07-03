<?php

namespace Reader\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    public function __construct(\Silex\Application $app)
    {
        parent::__construct('Reader', '1.0');

        $this->app=$app;

        $this->addCommands(array(
            new Command\AboutCommand(),
            new Command\CollectCommand()
        ));
    }

    public function getApp()
    {
        return $this->app;
    }

}
