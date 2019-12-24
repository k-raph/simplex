<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21/07/2019
 * Time: 11:56
 */

namespace App\JobeetModule\Admin\Actions;

use App\JobeetModule\Admin\Repository\CategoryRepository;
use App\JobeetModule\Entity\Category;
use Keiryo\DataMapper\EntityManager;
use Keiryo\Helper\Str;
use Keiryo\Helper\ValidatorTrait;
use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouterInterface;
use Simplex\Http\Session\SessionFlash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryManageAction
{

    use ValidatorTrait;

    /**
     * @var TwigRenderer
     */
    private $view;

    public function __construct(TwigRenderer $renderer)
    {
        $this->view = $renderer;
    }

    /**
     * @param CategoryRepository $repository
     * @return string
     */
    public function list(CategoryRepository $repository)
    {
        $categories = $repository->findAll();

        return $this->view->render('@jobeet/admin/category_list', compact('categories'));
    }

    public function create(Request $request, EntityManager $manager, SessionFlash $flash, RouterInterface $router)
    {
        if ('POST' === $request->getMethod()) {
            $data = $this->validate($request->request->all())->getValidData();
            $category = new Category($data['name']);
            $category->setSlug($data['slug'] ?? Str::slugify($data['name']));

            $manager->persist($category);
            $manager->flush();

            $flash->success('Category successfully created');
            return new RedirectResponse($router->generate('admin_jobeet_category_list'));
        }

        return $this->view->render('@jobeet/admin/category_form');
    }

    /**
     * @param int $id
     * @param EntityManager $manager
     * @return string
     */
    public function edit(int $id, EntityManager $manager)
    {
        $category = $manager->find(Category::class, $id);

        return $this->view->render('@jobeet/admin/category_form', compact('category'));
    }

    /**
     * @param Request $request
     * @param EntityManager $manager
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return RedirectResponse
     */
    public function update(Request $request, EntityManager $manager, RouterInterface $router, SessionFlash $flash)
    {
        $id = $request->attributes->get('_route_params')['id'];
        /** @var Category $category */
        $category = $manager->find(Category::class, $id);

        $data = $this->validate($request->request->all())->getValidData();
        $category->setName($data['name']);
        if ($data['slug']) {
            $category->setSlug($data['slug']);
        }

        $manager->persist($category);
        $manager->flush();

        $flash->success('Category successfully updated');
        return new RedirectResponse($router->generate('admin_jobeet_category_list'));
    }

    /**
     * @param int $id
     * @param CategoryRepository $repository
     * @param SessionFlash $flash
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function delete(int $id, CategoryRepository $repository, SessionFlash $flash, RouterInterface $router)
    {
        $repository->delete($id)
            ? $flash->success('Job successfully deleted')
            : $flash->error('An error occured');

        return new RedirectResponse($router->generate('admin_jobeet_category_list'));
    }

    /**
     * Get validation rules
     *
     * @return array
     */
    protected function getRules(): array
    {
        return [
            'name' => 'required|alpha_spaces',
            'slug' => 'alpha_dash'
        ];
    }
}
