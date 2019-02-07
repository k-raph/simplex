<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 10:33
 */

namespace App\JobeetModule\Repository;


use App\JobeetModule\Entity\Affiliate;
use Simplex\DataMapper\Repository\Repository;

class AffiliateRepository extends Repository
{

    /**
     * Performs an entity persistence
     *
     * @param Affiliate $affiliate
     */
    public function persist(Affiliate $affiliate)
    {
        $insert = [
            'name' => $affiliate->getName(),
            'email' => $affiliate->getEmail(),
            'url' => $affiliate->getUrl(),
            'is_active' => $affiliate->isActive(),
            'token' => $affiliate->getToken()
        ];

        $id = $this->query()->insertGetId($insert);
        $affiliate->setId($id);

        $maps = [];
        foreach ($affiliate->getCategories() as $category) {
            $maps[] = [
                'affiliate_id' => $id,
                'category_id' => $category
            ];
        }

        if (!empty($maps)) {
            $this->query()
                ->newQuery()
                ->table('affiliate_category')
                ->insert($maps);
        }
    }

}