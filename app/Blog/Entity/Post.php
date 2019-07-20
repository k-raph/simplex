<?php

namespace App\Blog\Entity;

use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\IdentifiableTrait;

class Post implements IdentifiableInterface
{

    use IdentifiableTrait;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $content;

    /**
     * @var User
     */
    private $author;

    /**
     * @var Comment[]
     */
    private $comments = [];

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return User
     */
    public function getAuthor()//: User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(/*User*/
        $author
    ): void
    {
        $this->author = $author;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param Comment[] $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }
}
