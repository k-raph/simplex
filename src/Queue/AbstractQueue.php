<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/07/2019
 * Time: 18:16
 */

namespace Simplex\Queue;


use Simplex\Queue\Contracts\JobInterface;
use Simplex\Queue\Contracts\QueueInterface;

abstract class AbstractQueue implements QueueInterface
{

    /**
     * Creates array payload to submit
     *
     * @param $job
     * @param string $queue
     * @return array
     * @throws \Exception
     */
    protected function createPayload($job, string $queue): array
    {
        $job = $job instanceof JobInterface ? $job = serialize($job) : $job;
        return [
            'id' => $this->getId(),
            'queue' => $queue,
            'job' => $job
        ];
    }

    /**
     * Get random id for job
     *
     * @return string
     * @throws \Exception
     */
    protected function getId(): string
    {
        $string = '';
        $length = 10;

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

}