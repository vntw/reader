<?php

namespace Reader\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;

/**
 * @Entity
 * @Table(name="subscription")
 **/
class Subscription
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=255)
     */
    private $name;

    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $icon;

    /**
     * @Column(type="string")
     */
    private $url;

    /**
     * @Column(type="string")
     */
    private $feedUrl;

    /**
     * @Column(type="smallint")
     */
    private $type;

    /**
     * @OneToMany(targetEntity="Item", mappedBy="subscription", fetch="EXTRA_LAZY")
     */
    private $items;

    /**
     * @ManyToMany(targetEntity="Tag", mappedBy="subscriptions")
     */
    private $tags;

    private $unreadItems;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string       $name
     * @return Subscription
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string       $icon
     * @return Subscription
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param  string       $url
     * @return Subscription
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string       $feedUrl
     * @return Subscription
     */
    public function setFeedUrl($feedUrl)
    {
        $this->feedUrl = $feedUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeedUrl()
    {
        return $this->feedUrl;
    }

    /**
     * @param  int          $type
     * @return Subscription
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Item $items
     */
    public function addItems(Item $items)
    {
        $this->items[] = $items;
    }

    /**
     * @return ItemCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $tag->addSubscription($this);
        $this->tags[] = $tag;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItems()->count() > 0;
    }

	/**
	 * @param  EntityManager $em
	 * @param  bool          $force
	 * @return int
	 */
	public function countUnreadItems(EntityManager $em, $force = false)
	{
		if (null === $this->unreadItems || $force) {
			$qb = $em->createQueryBuilder();
			$qb->select('count(i.read)')
				->from('Reader\\Entity\\Item', 'i')
				->where('i.read = 0')
				->andWhere('i.subscription = :id')
				->setParameter('id', $this->getId(), Type::INTEGER);

			$this->unreadItems = $qb->getQuery()->getSingleScalarResult();
		}

		return $this->unreadItems;
	}

	/**
	 * @param  EntityManager $em
	 */
	public function markItemsRead(EntityManager $em)
	{
		$qb = $em->createQueryBuilder();
		$q = $qb->update('Reader\\Entity\\Item', 'i')
			->set('i.read', true)
			->where('i.subscription = :id')
			->setParameter('id', $this->getId())
			->getQuery();
		$q->execute();
	}

    public function countItems()
    {

    }

    public function toArray()
    {
        $hash = array();

        $hash['id'] = $this->getId();
        $hash['name'] = $this->getName();
        $hash['icon'] = $this->getIcon();
        $hash['type'] = $this->getType();
        $hash['url'] = $this->getUrl();
        $hash['host'] = str_replace('www.', '', parse_url($this->getUrl(), PHP_URL_HOST));
        $hash['feedUrl'] = $this->getFeedUrl();

        return $hash;
    }
}
