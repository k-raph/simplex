<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/01/2019
 * Time: 18:53
 */

namespace App\Blog\Entity;

use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\IdentifiableTrait;

class Comment implements IdentifiableInterface
{

    use IdentifiableTrait;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $postId;

    /**
     * @var \DateTime
     */
    protected $createdAt;

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
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }


}