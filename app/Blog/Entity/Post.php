<?php

namespace App\Blog\Entity;

class Post
{
    private $id;
    private $title;
    private $slug;
    private $content;
    private $author;
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
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
