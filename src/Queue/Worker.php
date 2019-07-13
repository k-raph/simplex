<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/07/2019
 * Time: 21:47
 */

namespace Simplex\Queue;

use Simplex\Queue\Contracts\JobInterface;
use Simplex\Queue\Contracts\QueueInterface;

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
     * Listens to a queue for new jobs to run
     *
     * @param string $queue
     */
    public function listen(string $queue = 'default')
    {
        $connection = $this->manager->connection();
        while (true) {
            $job = $connection->pop($queue);
            if (null !== $job) {
                $this->runJob($job, $connection);
            } else {
                $this->sleep(5);
            }
        }
    }

    /**
     * @param JobInterface $job
     * @param QueueInterface $queue
     */
    protected function runJob(JobInterface $job, QueueInterface $queue)
    {
        try {
            $job->fire();
        } catch (\Exception $exception) {
            //$queue->bury($job);
            echo $exception->getMessage() . "\n";
        }
    }

    /**
     * @param int $seconds
     */
    protected function sleep(int $seconds)
    {
        sleep($seconds);
    }
}