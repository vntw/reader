<?php

namespace Reader\DataCollector;

use Reader\DataCollector\Type\Rss;
use Reader\DataCollector\Type\Twitter;
use Reader\Entity\Subscription;
use Silex\Application;

class Factory
{

    /**
     * @param  Subscription              $subscription
     * @param  Application               $app
     * @return Rss|Twitter
     * @throws \InvalidArgumentException
     */
    public static function fromSubscription(Subscription $subscription, Application $app)
    {
        switch ($subscription->getType()) {
            case DataCollectorInterface::TYPE_RSS:
                return new Rss($subscription, $app);
            case DataCollectorInterface::TYPE_TWITTER:
                return new Twitter($subscription, $app);
        }

        throw new \InvalidArgumentException(
            sprintf('Unsupported data collector type %s', $subscription->getType())
        );
    }
}
