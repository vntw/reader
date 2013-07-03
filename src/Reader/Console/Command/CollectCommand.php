<?php

namespace Reader\Console\Command;

use Reader\Console\Command\Command;
use Reader\DataCollector\Factory;
use Reader\Entity\Subscription;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CollectCommand extends Command
{
    protected function configure()
    {
        $this->setName('collect')
                ->setDescription('Collect your subscriptions')
                ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'ID of a specific subscription to update)', 0)
                ->addOption('mode', 'm', InputOption::VALUE_OPTIONAL, 'Update mode (everything, feed, icons)', 'all')
                ->addOption('quiet', 'q', InputOption::VALUE_NONE);
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $app = $this->getApplication()->getApp();

        $quiet = $input->getOption('quiet');

        if (!$quiet) {
            $output->writeln('<headline><> Collecting...</headline>');
        }

        $id = (int) $input->getOption('id');

        if ($id > 0) {
            $sub = $app['orm.em']->getRepository('Reader\\Entity\\Subscription')->find($id);

            if (!$sub instanceof Subscription) {
                $app->abort(404, 'no sub found with id: ' . $id);
            }

            $subscriptions = array($sub);
        } else {
            $subscriptions = $app['orm.em']->getRepository('Reader\\Entity\\Subscription')->findAll();
        }

        $results = array();

        foreach ($subscriptions as $subscription) {
            $collector = Factory::fromSubscription($subscription, $app);

            if (!$quiet) {
                $output->writeln(sprintf('Updating subscription: <result>%s</result>', $subscription->getName()));
            }

            $result = $collector->collect();

            if (!$quiet) {
                $output->writeln(sprintf('> <item>Added %d new items, got %d existing items</item>', $result['inserted'], $result['existing']));
            }

//			$results[$subscription->getId()] = $result;
//			$results[$subscription->getId()]['subscription'] = $subscription;
        }

        $output->writeln('<headline><> Finished!</headline>');
    }

}
