<?php

namespace Reader\Category;

use Silex\Application;

class TreeTwigExtension extends \Twig_Extension
{

    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('buildTree', array($this, 'getTree')),
        );
    }

    public function getTree()
    {
        $tree = new Tree($this->app['orm.em']);

        return $tree->build();
    }

    public function getName()
    {
        return 'categoryTree';
    }
}
