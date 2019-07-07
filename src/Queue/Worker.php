<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/07/2019
 * Time: 21:47
 */

namespace Simplex\Queue;


class Worker
{

    /**
     * @var QueueManager
     */
    private $manager;

    /**
     * Worker constructor.
     * @param QueueManager $manager
     */
    public function __construct(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     *
     */
    public function listen()
    {
        $queue = $this->manager->connection();
        while ($job = $queue->pop('default')) {
            $job->fire();
        }
    }
}