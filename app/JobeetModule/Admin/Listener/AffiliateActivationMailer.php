<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23/06/2019
 * Time: 21:06
 */

namespace App\JobeetModule\Admin\Listener;


use App\JobeetModule\Admin\Events\AffiliateActivationEvent;
use App\JobeetModule\Entity\Affiliate;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class AffiliateActivationMailer
{

    /**
     * @param AffiliateActivationEvent $event
     * @return AffiliateActivationEvent
     */
    public function handle(AffiliateActivationEvent $event): AffiliateActivationEvent
    {
        $affiliate = $event->getAffiliate();
        $mail = new Message();
        $mail->setFrom('admin@admin.fr')
            ->addTo($affiliate->getEmail())
            ->addReplyTo('admin@admin.fr')
            ->setSubject('Jobeet account activation')
            ->setHtmlBody($this->getBody($affiliate));

        $mailer = new SendmailMailer();
        $mailer->send($mail);

        return $event;
    }

    /**
     * Gets email body
     *
     * @param Affiliate $affiliate
     * @return string
     */
    protected function getBody(Affiliate $affiliate): string
    {
        return <<<HTML
Hello {$affiliate->getName()}.<br>
Here is your account activation token: <strong>{$affiliate->getToken()}</strong>. <br>
Please keep it in a secure place as it's for personal use only.<br>
Regards. Jobeet administrator
HTML;
    }
}