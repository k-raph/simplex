<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/02/2019
 * Time: 12:10
 */

namespace App\JobeetModule\Actions;


use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use Simplex\DataMapper\EntityManager;
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

    public function single(Request $request, EntityManager $manager, RouterInterface $router)
    {
        $slug = $request->attributes->get('_route_params')['slug'];
        $page = $request->query->get('page', 1);

        $category = $manager->getRepository(Category::class)->findOneBy(['slug' => $slug]);

        $query = $manager->createQueryBuilder(Job::class)->where(['category_id' => $category->getId()]);

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