<?php

namespace Simplex\Tests\DataMapper\Fixtures\Entity;

class Comment
{
    private $id;

    /**
     * @var string
     */
    private $content;

    /**
     * @var User
     */
    private $author;

    public function getId()
    {
        return $id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor()
    {
        return $this->author;
    }
}
