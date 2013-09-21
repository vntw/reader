<?php

namespace Reader\Subscription\Discovery;

use Reader\Util\SimplePie;

class Discovery
{

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
        $simplePie = new SimplePie($this->url);
        $simplePie->enable_cache(false);
        $simplePie->init();

        if ($simplePie->error()) {
            throw new \Exception($simplePie->error());
        }

        $feeds = array();

        foreach ($simplePie->get_all_discovered_feeds() as $link) {
            $feed = new \SimpleXMLElement($link->body);
            $title = (string) $feed->channel->title;

            $feeds[] = array(
                'title' => $title,
                'url' => $link->url
            );
        }

        return $feeds;
    }

}
