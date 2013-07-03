<?php

namespace Reader\Config;

class Config implements \ArrayAccess
{
    /**
     *
     * @var array
     */
    private $values;

    /**
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     *
     * @param  string            $file
     * @throws \RuntimeException
     */
    public static function load($file)
    {
        if (!file_exists($file)) {
            throw new \RuntimeException('Config file not found.');
        }

        $ext = 'yml';

        switch ($ext) {
            case 'yml':
                $loader = new YamlLoader();
                break;
            case 'json':
                $loader = new JsonLoader();
                break;
            default:
                throw new \RuntimeException('Unknown file type.');
        }

        $config = new self($loader->load($file));

        return $config;
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

}
