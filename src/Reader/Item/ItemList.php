<?php

namespace Reader\Item;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\Query;
use Symfony\Component\Finder\Expression\Expression;

class ItemList
{
	const TYPE_SUBSCRIPTION = 'subscription';
	const TYPE_TAG = 'tag';
	const TYPE_FAVOURITE = 'favourite';
	const TYPE_SAVED = 'saved';

	private $em;
	private $type;
	private $typeId;

	public function __construct(EntityManager $em, $type, $typeId = null)
	{
		$this->em = $em;
		$this->type = $type;
		$this->typeId = $typeId;
	}

	/**
	 * @param      $itemAmount
	 * @param int  $sort
	 * @param null $lastId
	 * @return array
	 */
	public function getItems($itemAmount, $sort = SORT_DESC, $lastId = null)
	{
		$qb = $this->buildQuery($itemAmount, $sort, $lastId);

		if ($qb instanceof AbstractQuery) {
			return $qb->getResult();
		} else {
			return $qb->getQuery()->getResult();
		}
	}

	protected function buildQuery($itemAmount, $sort = SORT_DESC, $lastId = null)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->select('i')
			->from('Reader\\Entity\\Item', 'i');

		switch ($this->type) {
			case self::TYPE_TAG:

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
			case self::TYPE_FAVOURITE:
				$qb->where('i.favourite = ?1')
					->setParameter(1, true);
				break;
			case self::TYPE_SAVED:
				$qb->where('i.saved = ?1')
					->setParameter(1, true);
				break;
			default:
				throw new \InvalidArgumentException('Invalid type.');
		}

//		if ($this->type === self::TYPE_SUBSCRIPTION) {
//			$qb->setParameter(1, $this->typeId);
//		} else if ($this->type !== self::TYPE_TAG) {
//
//		}

		if ($lastId) {
			$qb->andWhere('i.id > ?2')->setParameter(2, $lastId);
		}

		$qb->orderBy('i.date', ($sort === SORT_DESC) ? 'DESC' : 'ASC')
			->setMaxResults($itemAmount);
		echo "<pre>";
		var_dump($qb->getQuery());
		exit;

		return $qb;
	}

}