<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 19:29
 */

namespace App\JobeetModule\Admin\Repository;

use App\JobeetModule\Repository\AffiliateRepository as Repository;

class AffiliateRepository extends Repository
{

    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        return $this->mapper->findAll();
    }
}