<?php

namespace Reader\Importer\OPML;

class Result
{
    const TYPE_TAGS = 'tags';
    const TYPE_SUBSCRIPTIONS = 'subscriptions';

    private $errors;
    private $added;
    private $duplicates;

    public function __construct()
    {
        $this->errors = array();
        $this->added = array();
        $this->duplicates = array();
    }

    public function addError($type, $name)
    {
        $this->errors[$type][] = $name;
    }

    public function addAdded($type, $name)
    {
        $this->added[$type][] = $name;
    }

    public function addDuplicate($type, $name)
    {
        $this->duplicates[$type][] = $name;
    }

    public function toArray()
    {
        return array(
            'errors' => $this->errors,
            'added' => $this->added,
            'duplicates' => $this->duplicates
        );
    }
}
