<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/07/2019
 * Time: 21:24
 */

namespace App\JobeetModule\Admin\Jobs;

use App\JobeetModule\Entity\Affiliate;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Simplex\Queue\AbstractJob;

class ProcessSendEmail extends AbstractJob
{

    /**
     * @var Affiliate
     */
    private $affiliate;

    public function __construct(Affiliate $affiliate)
    {
        $this->affiliate = $affiliate;
    }

    /**
     * Executes the job
     *
     */
    public function fire()
    {
        $mail = new Message();
        $mail->setFrom('admin@admin.fr')
            ->addTo($this->affiliate->getEmail())
            ->addReplyTo('admin@admin.fr')
            ->setSubject('Jobeet account activation')
            ->setHtmlBody($this->getBody($this->affiliate));

        $mailer = new SendmailMailer();
        $mailer->send($mail);
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
