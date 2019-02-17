<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:23
 */

namespace App\JobeetModule\Entity;


use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\IdentifiableTrait;

class Category implements IdentifiableInterface
{

    use IdentifiableTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var array
     */
    private $jobs = [];

    /**
     * Category constructor.
     * @param string $name
     * @param string|null $slug
     */
    public function __construct(string $name, ?string $slug = null)
    {
        $this->name = $name;
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    /**
     * @param array $jobs
     */
    public function setJobs(array $jobs): void
    {
        $this->jobs = $jobs;
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

}