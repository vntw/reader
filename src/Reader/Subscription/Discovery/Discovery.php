<?php

namespace Reader\Subscription\Discovery;

use Reader\Util\SimplePie;

class Discovery
{

    const TYPE_MULTIPLE = 'multi';
    const TYPE_SINGLE = 'single';

    private $url;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function discover()
    {
        $singleFeed = null;

        try {
            $singleFeed = $this->getSingleFeed();
        } catch (\Exception $e) {

        }

        if ($singleFeed) {
            return array(
                'type' => self::TYPE_SINGLE,
                'feeds' => array(
                    array(
                        'title' => $singleFeed->get_title(),
                        'url' => $singleFeed->feed_url
                    )
                )
            );
        }

        $discoveredFeeds = $this->getDiscoverableFeeds();

        $feeds = array(
            'type' => self::TYPE_MULTIPLE,
            'feeds' => array()
        );

        foreach ($discoveredFeeds->get_all_discovered_feeds() as $link) {
            $feed = new \SimpleXMLElement($link->body);
            $title = (string) $feed->channel->title;

            $feeds['feeds'][] = array(
                'title' => $title,
                'url' => $link->url
            );
        }

        return $feeds;
    }

    /**
     * @return SimplePie
     * @throws \Exception
     */
    public function getSingleFeed()
    {
        $simplePie = new SimplePie($this->url);
        $simplePie->enable_cache(false);
        $simplePie->force_feed(true);
        $simplePie->set_autodiscovery_level(SIMPLEPIE_LOCATOR_NONE);
        @$simplePie->init();

        if ($error = $simplePie->error()) {
            throw new \Exception($error);
        }

        return $simplePie;
    }

    /**
     * @return SimplePie
     * @throws \Exception
     */
    public function getDiscoverableFeeds()
    {
        $simplePie = new SimplePie($this->url);
        $simplePie->enable_cache(false);
        $simplePie->force_feed(false);
        $simplePie->set_autodiscovery_level(SIMPLEPIE_LOCATOR_ALL);
        @$simplePie->init();

        if ($error = $simplePie->error()) {
            throw new \Exception($error);
        }

        return $simplePie;
    }

}
