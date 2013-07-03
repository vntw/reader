<?php

namespace Reader\Console\Command;

use Reader\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    protected function configure()
    {
        $this->setName('about')
            ->setDescription('Information about Reader');
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $output->writeln(sprintf('<headline><> %s %s</headline>', 'Reader', '1.0'));
        $output->writeln(<<<EOT
   Created by Sven Scheffler.
EOT
        );
    }

}
