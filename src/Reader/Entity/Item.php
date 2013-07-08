<?php

namespace Reader\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="item")
 **/
class Item
{

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Column(type="string", length=32, unique=true)
	 */
	private $uid;

	/**
	 * @Column(type="string", length=255)
	 */
	private $title;

	/**
	 * @Column(type="text")
	 */
	private $content;

	/**
	 * @Column(type="string", length=255,nullable=true)
	 */
	private $link;

	/**
	 * @Column(type="datetime")
	 */
	private $date;

	/**
	 * @ManyToOne(targetEntity="Subscription")
	 * @JoinColumn(name="subscription_id", referencedColumnName="id", nullable=false)
	 */
	private $subscription;

	/**
	 * @Column(name="rread", type="boolean")
	 */
	private $read;

	/**
	 * @Column(type="boolean")
	 */
	private $saved;

	/**
	 * @Column(type="boolean")
	 */
	private $favourite;

	public function __construct()
	{
		$this->date = new \DateTime();
		$this->subscription = new ArrayCollection();
		$this->read = false;
		$this->saved = false;
		$this->favourite = false;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param  string $uid
	 * @return Item
	 */
	public function setUid($uid)
	{
		$this->uid = $uid;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * @param  string $title
	 * @return Item
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param  string $content
	 * @return Item
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param  string $link
	 * @return Item
	 */
	public function setLink($link)
	{
		$this->link = $link;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param  \DateTime $date
	 * @return Item
	 */
	public function setDate(\DateTime $date)
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @param  Subscription $subscription
	 * @return Item
	 */
	public function setSubscription(Subscription $subscription)
	{
		$this->subscription = $subscription;

		return $this;
	}

	/**
	 * @return Subscription
	 */
	public function getSubscription()
	{
		return $this->subscription;
	}

	/**
	 * @param  bool $read
	 * @return Item
	 */
	public function setRead($read)
	{
		$this->read = $read;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getRead()
	{
		return $this->read;
	}

	/**
	 * @param bool $saved
	 * @return Item
	 */
	public function setSaved($saved)
	{
		$this->saved = $saved;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getSaved()
	{
		return $this->saved;
	}

	/**
	 * @param bool $favourite
	 * @return Item
	 */
	public function setFavourite($favourite)
	{
		$this->favourite = $favourite;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getFavourite()
	{
		return $this->favourite;
	}

	public function toArray()
	{
		$hash = array();

		$hash['id'] = $this->getId();
		$hash['uuid'] = $this->getUid();
		$hash['title'] = $this->getTitle();
		$hash['content'] = $this->getContent();
		$hash['link'] = $this->getLink();
		$hash['date'] = $this->getDate();

		$hash['favourite'] = $this->getFavourite();
		$hash['saved'] = $this->getSaved();
		$hash['read'] = $this->getRead();

		$hash['subscription'] = $this->getSubscription();

		return $hash;
	}

}
