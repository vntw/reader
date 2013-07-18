<?php

namespace Reader\Item;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\Query;
use Symfony\Component\Finder\Expression\Expression;

class ItemList
{
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_TAG = 'tag';
    const TYPE_FAVOURITES = 'favourites';
    const TYPE_SAVED = 'saved';
    const TYPE_HOME = 'home';

    private $em;
    private $type;
    private $typeId;
    private $sort;
    private $itemAmount;
    private $lastDate;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setItemAmount($itemAmount)
    {
        $this->itemAmount = $itemAmount;

        return $this;
    }

    public function getItemAmount()
    {
        return $this->itemAmount;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setType($type)
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException('Invalid type.');
        }

        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

    public function setLastDate($lastDate)
    {
        $this->lastDate = $lastDate;

        return $this;
    }

    public function getLastDate()
    {
        return $this->lastDate;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $qb = $this->buildQuery();

        if ($qb instanceof AbstractQuery) {
            return $qb->getResult();
        } else {
            return $qb->getQuery()->getResult();
        }
    }

    protected function buildQuery()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('i')
            ->from('Reader\\Entity\\Item', 'i');

        switch ($this->type) {
            case self::TYPE_TAG:
                echo "<pre>";
                var_dump(2211);
                exit;
                // TODO: FIX

                $rsm = new ResultSetMapping();
                $rsm->addEntityResult('Reader\\Entity\\Item', 'i')
                    ->addFieldResult('i', 'id', 'id')
                    ->addFieldResult('i', 'subscription_id', 'subscription_id')
                    ->addFieldResult('i', 'uid', 'uid')
                    ->addFieldResult('i', 'title', 'title')
                    ->addFieldResult('i', 'content', 'content')
                    ->addFieldResult('i', 'link', 'link')
                    ->addFieldResult('i', 'date', 'date')
                    ->addFieldResult('i', 'rread', 'rread')
                    ->addFieldResult('i', 'saved', 'saved')
                    ->addFieldResult('i', 'favourite', 'favourite');

                unset($qb);
                $qb = $this->em
                    ->createNativeQuery('SELECT i.id, i.title FROM item i JOIN tag_subscription ts ON ts.tag_id = i.subscription_id WHERE ts.tag_id = ?1', $rsm)
                    ->setParameter(1, $this->typeId);

//				$rsm = new Query\ResultSetMappingBuilder($this->em);

//				$rsm->addRootEntityFromClassMetadata('Reader\\Entity\\Item', 'i');
//				$rsm->addJoinedEntityFromClassMetadata('MyProject\Address', 'a', 'u', 'address', array('id' => 'address_id'));
//				echo "<pre>";
//				var_dump($this->typeId, $qb->getResult(), $qb);
//				exit;
                return $qb;
//				$values = array(2);

//				$qb->join('Reader\\Entity\\Subscription', 's', \Doctrine\ORM\Query\Expr\Join::WITH, 's.id = i.subscription')
//					->join('t.tag_subscription', 't', \Doctrine\ORM\Query\Expr\Join::WITH, 't.tag_id = s.tags')
//					->join('Reader\\Entity\\Tag', 't')
//					->where('t.id = ?1')
//					->andWhere($qb->expr()->in('t.id', $values)) // here $values['value'] will be a collection of objects so maybe you will have to transform it into an array of ids to make the `in` expression work correctly.
//					->setParameter(1, $this->typeId);
                break;
            case self::TYPE_SUBSCRIPTION:
                $qb->where('i.subscription = ?1')
                    ->setParameter(1, $this->typeId);
                break;
            case self::TYPE_FAVOURITES:
                $qb->where('i.favourite = ?1')
                    ->setParameter(1, true);
                break;
            case self::TYPE_SAVED:
                $qb->where('i.saved = ?1')
                    ->setParameter(1, true);
                break;
        }

        if ($this->lastDate) {
            $lastDate = date("Y-m-d H:i:s", $this->lastDate);

            if ($this->sort === SORT_DESC) {
                $qb->andWhere('i.date < ?2')->setParameter(2, $lastDate);
            } else {
                $qb->andWhere('i.date > ?2')->setParameter(2, $lastDate);
            }
        }

        $qb->orderBy('i.date', ($this->sort === SORT_DESC) ? 'DESC' : 'ASC')
            ->setMaxResults($this->itemAmount);

//		echo "<pre>";
//		var_dump($qb->getQuery());
//		exit;
        return $qb;
    }

    private function isValidType($type)
    {
        return in_array($type, array(
            self::TYPE_FAVOURITES,
            self::TYPE_SAVED,
            self::TYPE_TAG,
            self::TYPE_SUBSCRIPTION,
            self::TYPE_HOME
        ));
    }

}
