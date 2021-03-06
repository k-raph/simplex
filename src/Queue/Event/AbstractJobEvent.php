<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/07/2019
 * Time: 17:00
 */

namespace Simplex\Queue\Event;

use Simplex\Queue\Contracts\JobInterface;

abstract class AbstractJobEvent
{

    /**
     * @var JobInterface
     */
    private $job;

    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    /**
     * @return JobInterface
     */
    public function getJob(): JobInterface
    {
        return $this->job;
    }
}
