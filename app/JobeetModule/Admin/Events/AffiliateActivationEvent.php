<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23/06/2019
 * Time: 21:05
 */

namespace App\JobeetModule\Admin\Events;


use App\JobeetModule\Entity\Affiliate;

class AffiliateActivationEvent
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
     * @return Affiliate
     */
    public function getAffiliate(): Affiliate
    {
        return $this->affiliate;
    }

}