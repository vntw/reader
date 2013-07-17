<?php

namespace Reader\Util;

class SimplePie extends \SimplePie
{

    /**
     * @param string|null $url
     */
    public function __construct($url = null)
    {
        parent::__construct();

        $this->initCache();
        $this->force_feed(true);

        if (null !== $url) {
            $this->set_feed_url($url);
        }

    }

    /**
     * Set the cache location + lifetime
     */
    private function initCache()
    {
        $cache = __DIR__ . '/../../../cache/simplepie';

        if (!is_dir($cache)) {
            mkdir($cache, 0777, true);
        }

        $this->set_cache_location($cache);
        $this->set_cache_duration(1800);
    }

}
