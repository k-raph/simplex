<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/02/2019
 * Time: 12:10
 */

namespace App\JobeetModule\Actions;


use App\JobeetModule\Entity\Category;
use App\JobeetModule\Repository\CategoryRepository;
use App\JobeetModule\Repository\JobRepository;
use Simplex\Pagination\Paginator;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryShowAction
{

    /**
     * @var TwigRenderer
     */
    private $view;

    public function __construct(TwigRenderer $view)
    {
        $this->view = $view;
    }

    public function single(Request $request, CategoryRepository $repository, JobRepository $jobRepository, RouterInterface $router)
    {
        $slug = $request->attributes->get('_route_params')['slug'];
        $page = $request->query->get('page', 1);

        /** @var Category $category */
        $category = $repository->findOneBy(['slug' => $slug]);

        $query = $jobRepository->activeQuery($category->getId());
        $paginator = new Paginator();
        $paginator
            ->withUrl($router->generate('category_show', ['slug' => $category->getSlug()]))
            ->paginate($query, $page, 5);
        $category->setJobs($paginator->getItems());

        return $this->view->render('@jobeet/category/show', [
            'category' => $category,
            'pages' => $paginator
        ]);
    }

}