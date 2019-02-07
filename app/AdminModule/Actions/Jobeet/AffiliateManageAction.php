<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 15:43
 */

namespace App\AdminModule\Actions\Jobeet;


use App\JobeetModule\Entity\Affiliate;
use Simplex\DataMapper\EntityManager;
use Simplex\Renderer\TwigRenderer;
use Tracy\Debugger;

class AffiliateManageAction
{

    public function list(TwigRenderer $renderer, EntityManager $manager)
    {
        Debugger::barDump($manager->getRepository(Affiliate::class)
            ->findAll());

        return 'H';
    }

}