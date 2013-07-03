<?php

namespace Reader\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * @Entity
 * @Table(name="tag")
 **/
class Tag
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=50)
     */
    private $name;

    /**
     * @Column(type="string", length=7, nullable=true)
     */
    private $color;

    /**
     * @ManyToMany(targetEntity="Subscription", inversedBy="tags")
     */
    private $subscriptions;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function addSubscription(Subscription $subscription)
    {
        $this->subscriptions[] = $subscription;
    }

    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function countUnreadItems(EntityManager $em)
    {
        $unread = 0;

        foreach ($this->getSubscriptions() as $subscription) {
            $unread += $subscription->countUnreadItems($em);
        }

        return $unread;
    }

    public function toArray()
    {
        $hash = array();

        $hash['id'] = $this->getId();
        $hash['name'] = $this->getName();
        $hash['color'] = $this->getColor();

        return $hash;
    }

}
