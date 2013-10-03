<?php

namespace Reader\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends FileLoader
{

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function load($resource, $type = null)
    {

        $path = $this->locator->locate($resource);

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new FileNotFoundException(sprintf('File "%s" not found.', $path));
        }

        try {
            $configurations = Yaml::parse(file_get_contents($path));
        } catch (ParseException $e) {
            throw new \InvalidArgumentException('Error parsing YAML.', 0, $e);
        }

        if (null === $configurations) {
            $configurations = array();
        }

        return new Config($configurations);
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }

}
