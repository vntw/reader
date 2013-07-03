<?php

namespace Reader\Importer\OPML;

use Doctrine\ORM\EntityManager;
use Reader\Importer\OPML\Result;
use Reader\Entity\Subscription;
use Reader\Entity\Tag;
use Reader\DataCollector\DataCollectorInterface;

class Parser
{
    private $em;
    private $xmlData;
    private $stats;
    private $result;
    private $tagCache;

    public function __construct(EntityManager $em, $xmlData)
    {
        $this->em = $em;
        $this->stats = array();
        $this->tagCache = array();
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
     * @param array             $tags
     */
    private function parseGroup(\SimpleXMLElement $group, array $tags = array())
    {
        // tags are the words of the outline parent
        if ($group->getName() === 'outline' && (string) $group['title'] && $group['title'] != '/') {
            $tags[] = (string) $group['title'];
        }

        // parse every outline item
        foreach ($group->outline as $outline) {
            if ((string) $outline['type']) {
                if ($outline['type'] == 'folder') {
                    $this->parseGroup($outline, $tags);
                } else {
                    $this->createSubscription($outline, $tags);
                }
            } else {
                $this->parseGroup($outline, $tags);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $outline
     * @param array             $tags
     */
    private function createSubscription(\SimpleXMLElement $outline, array $tags)
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

        foreach ($tags as $aTag) {
            if (null !== ($tag = $this->getTag($aTag))) {
                $subscription->addTag($tag);
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
     * @return Tag
     */
    private function getTag($name)
    {
        if (isset($this->tagCache[$name])) {
            return $this->tagCache[$name];
        }

        $tag = $this->em->getRepository('Reader\\Entity\\Tag')->findOneBy(array('name' => $name));

        if (!$tag instanceof Tag && strlen($name) > 0) {
            $tag = new Tag();
            $tag->setName($name);

            $this->result->addAdded(Result::TYPE_TAGS, $name);
            $this->em->persist($tag);
        } else {
            $this->result->addDuplicate(Result::TYPE_TAGS, $name);
        }

        $this->tagCache[$name] = $tag;

        return $tag;
    }
}
