<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23/06/2019
 * Time: 21:06
 */

namespace App\JobeetModule\Admin\Listener;

use App\JobeetModule\Admin\Events\AffiliateActivationEvent;
use App\JobeetModule\Admin\Jobs\ProcessSendEmail;
use Keiryo\Queue\Contracts\QueueInterface;

class AffiliateActivationMailer
{

    /**
     * @param AffiliateActivationEvent $event
     * @param QueueInterface $queue
     * @return AffiliateActivationEvent
     */
    public function handle(AffiliateActivationEvent $event, QueueInterface $queue): AffiliateActivationEvent
    {
        $affiliate = $event->getAffiliate();
        $job = new ProcessSendEmail($affiliate);
        $queue->push($job, 'default');

        return $event;
    }
}
