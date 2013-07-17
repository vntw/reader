<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ven
 * Date: 18.06.13
 * Time: 22:04
 * To change this template use File | Settings | File Templates.
 */

namespace Reader\Tag;

use Doctrine\ORM\EntityManager;

class Tree
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function build()
    {
        $tagTree = array();

        foreach ($this->em->getRepository('Reader\\Entity\\Tag')->findAll() as $tag) {
            /* @var $tag \Reader\Entity\Tag */
            $tagTree[$tag->getId()] = $tag->toArray();

            $unread = 0;
            foreach ($tag->getSubscriptions() as $subscription) {
                $unread += $subscription->countUnreadItems($this->em);

                $tagTree[$tag->getId()]['subs'][$subscription->getId()] = $subscription->toArray();
                $tagTree[$tag->getId()]['subs'][$subscription->getId()]['unread'] = $subscription->countUnreadItems($this->em);
            }

            $tagTree[$tag->getId()]['unread'] = $unread;
        }

        return $tagTree;
    }

}
