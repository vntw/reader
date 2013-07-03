<?php

namespace Reader\Console\Command;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->injectAdditionalStyles($output);
    }

    /**
     * Add additional console output styles
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function injectAdditionalStyles(OutputInterface $output)
    {
        $output->getFormatter()->setStyle(
            'headline', new OutputFormatterStyle('green', null, array('bold'))
        );
        $output->getFormatter()->setStyle(
            'result', new OutputFormatterStyle('white', null, array('bold'))
        );
        $output->getFormatter()->setStyle(
            'item', new OutputFormatterStyle('white')
        );
    }

}
