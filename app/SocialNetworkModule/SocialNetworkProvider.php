<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30/05/2019
 * Time: 07:46
 */

namespace App\SocialNetworkModule;

use Simplex\Module\AbstractModule;

class SocialNetworkProvider extends AbstractModule
{

    public function __construct()
    {

    }

    /**
     * Get module short name for searching in config
     *
     * @return string
     */
    public function getName(): string
    {
        return 'social_network';
    }
}