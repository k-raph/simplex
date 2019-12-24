<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20/07/2019
 * Time: 21:08
 */

namespace App\JobeetModule\Admin\Actions;

use App\JobeetModule\Admin\Repository\JobRepository;
use App\JobeetModule\Entity\Category;
use App\JobeetModule\Entity\Job;
use App\JobeetModule\Mapper\JobMapper;
use App\JobeetModule\Repository\CategoryRepository;
use Keiryo\DataMapper\EntityManager;
use Keiryo\Helper\ValidatorTrait;
use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouterInterface;
use Simplex\Configuration\Configuration;
use Simplex\Http\Session\SessionFlash;
use Simplex\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class JobManageAction
{

    use ValidatorTrait;

    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * @var int
     */
    private $perPage;

    /**
     * JobManageAction constructor.
     * @param TwigRenderer $renderer
     * @param Configuration $configuration
     */
    public function __construct(TwigRenderer $renderer, Configuration $configuration)
    {
        $this->view = $renderer;
        $this->perPage = $configuration->get('jobeet.admin_jobs_per_page', 10);
    }

    /**
     * @param Request $request
     * @param JobRepository $jobRepository
     * @param RouterInterface $router
     * @return string
     */
    public function list(Request $request, JobRepository $jobRepository, RouterInterface $router)
    {
        $page = $request->query->get('page', 1);
        $jobs = $jobRepository->findAll();

        $paginator = (new Paginator())->withUrl($router->generate('admin_jobeet_job_list'))
            ->paginate($jobs, $page, $this->perPage);

        return $this->view->render('@jobeet/admin/jobs_list', [
            'jobs' => $paginator->getItems(),
            'pages' => $paginator
        ]);
    }

    /**
     * @param int $id
     * @param EntityManager $manager
     * @return string
     */
    public function single(int $id, EntityManager $manager)
    {
        return $this->view->render('@jobeet/admin/job_show', [
            'job' => $manager->find(Job::class, $id)
        ]);
    }

    /**
     * @param int $id
     * @param EntityManager $em
     * @param CategoryRepository $repository
     * @return string
     */
    public function edit(int $id, EntityManager $em, CategoryRepository $repository)
    {
        /** @var JobMapper $manager */
        $manager = $em->getMapper(Job::class);

        return $this->view->render('@jobeet/admin/job_form', [
            'job' => $manager->find($id),
            'types' => array_flip(Job::TYPES),
            'categories' => $repository->getAllForForm()
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManager $manager
     * @param CategoryRepository $categoryRepository
     * @param SessionFlash $flash
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function update(
        Request $request,
        EntityManager $manager,
        CategoryRepository $categoryRepository,
        SessionFlash $flash,
        RouterInterface $router
    )
    {
        $id = $request->attributes->get('_route_params')['id'];
        $this->categories = $categoryRepository->getAllForForm();
        $data = $this->validate($request->request->all())->getValidData();

        $job = $manager->find(Job::class, $id);
        $this->hydrate($job, $data);
        $manager->persist($job);
        $manager->flush();

        $flash->success('Job successfully updated');
        return new RedirectResponse($router->generate('admin_jobeet_job_list'));
    }

    /**
     * Hydrates an entity
     *
     * @param Job $job
     * @param array $input
     */
    protected function hydrate(Job $job, array $input)
    {
        $job->setEmail($input['email']);
        $job->setApplication($input['application']);
        $job->setType($input['type']);
        $job->setUrl(empty($input['url']) ? null : $input['url']);
        $job->setPosition($input['position']);
        $job->setLocation($input['location']);
        $job->setDescription($input['description']);
        $job->setCompany($input['company']);
        $job->setPublic(1 === (int)$input['public']);

        $category = current(array_filter(
            $this->categories,
            function (array $category) use ($input) {
                return $input['category'] == $category['slug'];
            }
        ));

        $id = $category['slug'];
        $category = new Category($category['name']);
        $category->setId($id);
        $job->setCategory($category);
    }

    /**
     * @param int $id
     * @param JobRepository $repository
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return RedirectResponse
     */
    public function delete(int $id, JobRepository $repository, RouterInterface $router, SessionFlash $flash)
    {
        $repository->delete($id)
            ? $flash->success('Job successfully deleted')
            : $flash->error('An error occured');

        return new RedirectResponse($router->generate('admin_jobeet_job_list'));
    }

    /**
     * Get validation rules
     *
     * @return array
     */
    protected function getRules(): array
    {
        $categories = array_map(function (array $category) {
            return $category['slug'];
        }, $this->categories);
        $categories = implode(',', $categories);

        $types = implode(',', array_keys(Job::TYPES));

        return [
            'company' => 'required',
            'url' => 'url',
            'position' => 'required',
            'location' => 'required',
            'description' => 'required',
            'application' => 'required',
            'email' => 'required|email',
            'public' => 'default:0|required|in:0,1',
            'category' => "required|in:$categories",
            'type' => "required|in:$types"
        ];
    }
}
