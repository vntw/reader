<?php

namespace Reader\DataCollector\Type;

use Reader\DataCollector\DataCollector;

class Twitter extends DataCollector
{

    public function getName()
    {
        return 'Twitter Stream';
    }

    public function getDescription()
    {
        return 'Twitter Feed Type';
    }

    public function collect()
    {
        // TODO: Implement collect() method.
    }
}
