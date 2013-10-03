<?php

namespace Reader\Importer\OPML;

use Doctrine\ORM\EntityManager;
use Reader\Importer\OPML\Result;
use Reader\Entity\Subscription;
use Reader\Entity\Category;
use Reader\DataCollector\DataCollectorInterface;

class Parser
{
    private $em;
    private $xmlData;
    private $stats;
    private $result;
    private $catCache;

    public function __construct(EntityManager $em, $xmlData)
    {
        $this->em = $em;
        $this->stats = array();
        $this->catCache = array();
        $this->result = new Result();

        if ($xmlData instanceof \SplFileInfo) {
            $this->xmlData = file_get_contents($xmlData);
        } else {
            $this->xmlData = $xmlData;
        }
    }

    public function getResult()
    {
        return $this->result;
    }

    public function parse()
    {
        if (!$this->xmlData) {
            throw new \InvalidArgumentException('Invalid XML data!');
        }

        $xml = new \SimpleXMLElement($this->xmlData);

        $this->em->beginTransaction();

        $this->parseGroup($xml->body);

        $this->em->flush();
        $this->em->commit();
    }

    /**
     * @param \SimpleXMLElement $group
     * @param array             $categories
     */
    private function parseGroup(\SimpleXMLElement $group, array $categories = array())
    {
        // tags are the words of the outline parent
        if ($group->getName() === 'outline' && (string) $group['title'] && $group['title'] != '/') {
            $categories[] = (string) $group['title'];
        }

        // parse every outline item
        foreach ($group->outline as $outline) {
            if ((string) $outline['type']) {
                if ($outline['type'] == 'folder') {
                    $this->parseGroup($outline, $categories);
                } else {
                    $this->createSubscription($outline, $categories);
                }
            } else {
                $this->parseGroup($outline, $categories);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $outline
     * @param array             $categories
     */
    private function createSubscription(\SimpleXMLElement $outline, array $categories)
    {
        $title = (string) $outline->attributes()->title;
        $url = (string) $outline->attributes()->htmlUrl;
        $feedUrl = (string) $outline->attributes()->xmlUrl;
        $type = (string) $outline->attributes()->type;

        $subscription = new Subscription();
        $subscription->setName($title)
            ->setUrl($url)
            ->setFeedUrl($feedUrl);

        switch ($type) {
            case 'rss':
            default:
                $subscription->setType(DataCollectorInterface::TYPE_RSS);
        }

        foreach ($categories as $aCat) {
            if (null !== ($cat = $this->getCategory($aCat))) {
                $subscription->setCategory($cat);
            }
        }

        $criteria = array(
            'name' => $title,
            'url' => $url,
            'feedUrl' => $feedUrl
        );

        if (!$this->em->getRepository('Reader\\Entity\\Subscription')->findBy($criteria)) {
            $this->em->persist($subscription);

            $this->result->addAdded(Result::TYPE_SUBSCRIPTIONS, $title);
        } else {
            $this->result->addDuplicate(Result::TYPE_SUBSCRIPTIONS, $title);
        }
    }

    /**
     * @param  string $name
     * @return Category
     */
    private function getCategory($name)
    {
        if (isset($this->catCache[$name])) {
            return $this->catCache[$name];
        }

        $category = $this->em->getRepository('Reader\\Entity\\Category')->findOneBy(array('name' => $name));

        if (!$category) {
            $category = new Category();
            $category->setName($name);

            $this->result->addAdded(Result::TYPE_CATEGORIES, $name);
            $this->em->persist($category);

            $this->catCache[$name] = $category;
        } else {
            $this->result->addDuplicate(Result::TYPE_CATEGORIES, $name);
        }

        return $category;
    }
}
