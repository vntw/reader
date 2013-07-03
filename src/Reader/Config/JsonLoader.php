<?php

namespace Reader\Config;

class JsonLoader extends Loader
{
    public function load($file)
    {
        $json = json_decode(file_get_contents($file), true);

        if (!$json) {
            throw new \RuntimeException('Error reading config file.');
        }

        return $json;
    }

}
