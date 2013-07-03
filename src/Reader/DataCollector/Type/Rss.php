<?php
namespace Reader\DataCollector\Type;

use Reader\DataCollector\DataCollector;
use Reader\Entity\Item;
use Reader\Util\FavIcon;
use Reader\Util\SimplePie;

class Rss extends DataCollector
{

    public function getName()
    {
        return 'RSS Feed';
    }

    public function getDescription()
    {
        return 'RSS Feed Type';
    }

    public function collect()
    {
        $stats = array(
            'updatedIcon' => false,
            'inserted' => 0,
            'existing' => 0,
            'error' => null
        );
        $entityManager = $this->app['orm.em'];
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $simplePie = new SimplePie($this->subscription->getFeedUrl());
        $simplePie->init();

        if ($simplePie->error()) {
            $stats['error'] = $simplePie->error();

            return $stats;
        }

        $favicon = new FavIcon($this->subscription->getUrl());
        $favicon->fetch();

        if ($favicon->hasIcons()) {
            $bestFit = $favicon->getBestAvailable();
            $icon = $favicon->save($bestFit);

            if ($icon) {
                $stats['updatedIcon'] = true;
                $this->subscription->setIcon($icon);
            } else {
                $this->subscription->setIcon(null);
            }

            $entityManager->persist($this->subscription);
        }

        $runtimeDuplicates = array();
        $feedItems = $simplePie->get_items();

        foreach ($feedItems as $feedItem) {
            /* @var $feedItem \SimplePie_Item */

            $itemId = $feedItem->get_id(true);

            // exists item?
            $existing = $entityManager->getRepository('Reader\\Entity\\Item')->findOneBy(array('uid' => $itemId));

            if ($existing || in_array($itemId, $runtimeDuplicates)) {
                $stats['existing']++;
                continue;
            } else {
                $runtimeDuplicates[] = $itemId;
            }

            $itemDate = $feedItem->get_date('Y-m-d H:i:s') ? : 'now';
            $content = $feedItem->get_content();

            $config = \HTMLPurifier_Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $content = $purifier->purify($content);

            $item = new Item();
            $item->setUid($itemId)
                ->setTitle($feedItem->get_title())
                ->setSubscription($this->subscription)
                ->setDate(new \DateTime($itemDate))
                ->setLink($feedItem->get_link())
                ->setContent($content)
                ->setUnread(true);

            $entityManager->persist($item);
            $stats['inserted']++;
        }

        $entityManager->flush();

        return $stats;
    }
}
