<?php

namespace Reader\DataCollector;

use Reader\Entity\Subscription;
use Silex\Application;

abstract class DataCollector implements DataCollectorInterface
{

    /**
     * @var \Reader\Entity\Subscription
     */
    protected $subscription;

    /**
     * @var \Silex\Application
     */
    protected $app;

    /**
     * @param Subscription $subscription
     * @param Application  $app
     */
    public function __construct(Subscription $subscription = null, Application $app = null)
    {
        $this->subscription = $subscription;
        $this->app = $app;
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return string
     */
    abstract public function getDescription();

    /**
     * @param Subscription $subscription
     */
    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param \Silex\Application $app
     */
    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return \Silex\Application
     */
    public function getApp()
    {
        return $this->app;
    }

    public function cleanup()
    {

    }

    /**
     * @return mixed
     */
    abstract public function collect();
}
