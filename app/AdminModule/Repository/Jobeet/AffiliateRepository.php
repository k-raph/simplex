<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 19:29
 */

namespace App\AdminModule\Repository\Jobeet;

use App\JobeetModule\Repository\AffiliateRepository as Repository;

class AffiliateRepository extends Repository
{

    /**
     * @inheritdoc
     */
    public function findAll(): array
    {
        return $this->query()
            ->addSelect(['id', 'name', 'email', 'url', 'is_active'])
            ->get();
    }
}