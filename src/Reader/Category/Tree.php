<?php

namespace Reader\Category;

use Doctrine\ORM\EntityManager;
use Reader\Entity\Category;
use Reader\Entity\Subscription;

class Tree
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $tree;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function build()
    {
        $this->tree = array();

        foreach ($this->em->getRepository('Reader\\Entity\\Category')->findAll() as $category) {
            /* @var $category \Reader\Entity\Category */
            $this->tree[$category->getId()] = $category->toArray();

            $unread = 0;
            $this->tree[$category->getId()]['subs'] = array();

            foreach ($category->getSubscriptions() as $subscription) {
                $unread += $subscription->countUnreadItems($this->em);

                $this->addSubscription($category->getId(), $subscription);
            }

            $this->tree[$category->getId()]['unread'] = $unread;
        }

        $lonelySubscriptions = $this->em->getRepository('Reader\\Entity\\Subscription')->findBy(array(
            'category' => null
        ));

        if (count($lonelySubscriptions) > 0) {
            $category = new Category();
            $category->setId(0)
                ->setName('Lonely Subs');

            $unread = 0;
            $this->tree[$category->getId()] = $category->toArray();

            foreach ($lonelySubscriptions as $sub) {
                $unread += $sub->countUnreadItems($this->em);
                $this->addSubscription(0, $sub);
            }

            $this->tree[$category->getId()]['unread'] = $unread;
        }

        return $this->tree;
    }

    /**
     * @param int          $catId
     * @param Subscription $subscription
     */
    private function addSubscription($catId, Subscription $subscription)
    {
        $this->tree[$catId]['subs'][$subscription->getId()] = $subscription->toArray();
        $this->tree[$catId]['subs'][$subscription->getId()]['unread'] = $subscription->countUnreadItems($this->em);
    }

}
