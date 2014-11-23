<?php

class Articles extends \Phalcon\Mvc\Model {

public $id;
public $title;
public $body;

public function getId()    {
    return $this->id;
}

public function getTitle()    {
    return $this->title;
}

public function getBody()    {
    return $this->body;
}

}