<?php

namespace Reader\DataCollector;

interface DataCollectorInterface
{

    const TYPE_RSS = 1;
    const TYPE_TWITTER = 2;

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function collect();
}
