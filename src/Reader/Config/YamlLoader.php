<?php

namespace Reader\Config;

use Symfony\Component\Yaml\Yaml;

class YamlLoader extends Loader
{
    public function load($file)
    {
        return Yaml::parse(file_get_contents($file), true);
    }

}
