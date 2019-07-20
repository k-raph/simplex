<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:06
 */

namespace App\JobeetModule\Actions;

use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use App\JobeetModule\Repository\CategoryRepository;
use Simplex\Database\Exceptions\ResourceNotFoundException;
use Simplex\DataMapper\EntityManager;
use Simplex\Renderer\TwigRenderer;

class JobShowAction
{

    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * JobController constructor.
     * @param TwigRenderer $view
     */
    public function __construct(TwigRenderer $view)
    {
        $this->view = $view;
    }

    /**
     * @param CategoryRepository $repository
     * @return string
     */
    public function all(CategoryRepository $repository)
    {
        /** @var Category[] $categories */
        $categories = $repository->getWithActiveJobs();

        return $this->view->render('@jobeet/job/list', compact('categories'));
    }

    /**
     * @param int $id
     * @param EntityManager $manager
     * @return string
     * @throws ResourceNotFoundException
     */
    public function show(int $id, EntityManager $manager)
    {
        $job = $manager->getMapper(Job::class)->find($id);

        if (!$job) {
            throw new ResourceNotFoundException();
        }
        return $this->view->render('@jobeet/job/show', compact('job'));
    }
}
