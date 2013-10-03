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
