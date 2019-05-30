<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05/05/2019
 * Time: 13:59
 */

namespace App\JobeetModule\Actions;

use Simplex\DataMapper\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class SearchAction
{

    public function perform(Request $request, EntityManager $manager)
    {
        //TODO : Implement search algorithm based on sqlite database
        /*$filter = $request->request->get('search', '');
        $result = $manager->getConnection()->query("SELECT * FROM temp_jobs WHERE temp_jobs MATCH 'location:West'")//, [':search' => $filter])
            ->fetchAll();

        return new JsonResponse($result);*/
    }

}