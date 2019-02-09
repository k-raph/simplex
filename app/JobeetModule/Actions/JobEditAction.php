<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/02/2019
 * Time: 20:41
 */

namespace App\JobeetModule\Actions;

use App\JobeetModule\Entity\Job;
use App\JobeetModule\Repository\JobRepository;
use Rakit\Validation\ErrorBag;
use Simplex\Configuration\Configuration;
use Simplex\Database\Query\Builder;
use Simplex\DataMapper\EntityManager;
use Simplex\Helper\ValidatorTrait;
use Simplex\Http\Session\SessionFlash;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use Simplex\Security\Csrf\CsrfTokenManager;
use Simplex\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class JobEditAction
{

    use ValidatorTrait;

    /**
     * @var array|array[]
     */
    private $categories;
    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * @var string
     */
    private $dest;

    /**
     * JobEditAction constructor.
     * @param TwigRenderer $view
     * @param Builder $query
     * @param Configuration $config
     * @throws \Throwable
     */
    public function __construct(TwigRenderer $view, Builder $query, Configuration $config)
    {
        $this->view = $view;
        $this->dest = $config->get('resources') . '/uploads';
        $this->categories = $query->table('categories', 'c')
            ->addSelect('id', 'slug')
            ->addSelect('name')
            ->get();
    }

    /**
     * Render creation form
     *
     * @return string
     */
    public function new()
    {
        $categories = $this->categories;
        $types = array_flip(Job::TYPES);

        return $this->view->render('@jobeet/job/new', compact('categories', 'types'));
    }

    /**
     * @param Request $request
     * @param EntityManager $manager
     * @param RouterInterface $router
     * @return RedirectResponse
     * @throws \Exception
     */
    public function create(Request $request, EntityManager $manager, RouterInterface $router)
    {
        $input = $this->sanitize($request);

        $job = new Job();
        $this->hydrate($job, $input);

        $job->setCreatedAt(new \DateTime());
        $job->setLogo($input['logo']);
        $job->setToken((new CsrfTokenManager)->generateToken());

        $manager->persist($job);
        $manager->flush();

        return new RedirectResponse($router->generate('job_preview', ['token' => $job->getToken()]));
    }

    /**
     * Performs validation and file move if necessary
     *
     * @param Request $request
     * @return array
     */
    protected function sanitize(Request $request): array
    {
        $input = $this->validate($request->request->all())->getValidData();
        $input['logo'] = null;
        /** @var UploadedFile|null $logo */
        $logo = $request->files->get('logo');

        if ($logo) {
            $this->verify($logo);
            $company = preg_replace("~\W+~", '_', $input['company']);
            $name = trim(strtolower($company), '_') . '_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move($this->dest, $name);
            $input['logo'] = $name;
        }

        return $input;
    }

    /**
     * @param UploadedFile $logo
     */
    protected function verify(UploadedFile $logo)
    {
        if (!in_array($logo->getClientOriginalExtension(), ['jpeg', 'jpg', 'png'])) {
            throw new ValidationException(new ErrorBag(['logo' => 'Logo file must be of type "jpeg", "jpg" or "png"']));
        }

        if ($logo->getClientSize() > (500 * 1024)) {
            throw new ValidationException(new ErrorBag(['logo' => 'Logo file size must be lower than 500K']));
        }
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
        $job->setUrl($input['url'] ?? null);
        $job->setLogo($input['logo']);
        $job->setPosition($input['position']);
        $job->setLocation($input['location']);
        $job->setDescription($input['description']);
        $job->setCompany($input['company']);
        $job->setPublic(1 === (int)$input['public']);
        $job->setCategory($input['category']);
    }

    /**
     * Render edition view
     *
     * @param string $token
     * @param JobRepository $repository
     * @return string
     */
    public function edit(string $token, JobRepository $repository)
    {
        $job = $repository->findByToken($token);

        $categories = $this->categories;
        $types = array_flip(Job::TYPES);

        return $this->view->render('@jobeet/job/new', compact('job', 'categories', 'types'));
    }

    /**
     * Performs entity update
     *
     * @param Request $request
     * @param JobRepository $repository
     * @param SessionFlash $flash
     * @param RouterInterface $router
     * @return RedirectResponse
     * @throws \Simplex\Database\Exceptions\ResourceNotFoundException
     */
    public function update(Request $request, JobRepository $repository, SessionFlash $flash, RouterInterface $router)
    {
        $input = $this->sanitize($request);
        $token = $request->attributes->get('_route_params')['token'];
        /** @var Job $job */
        $job = $repository->findByToken($token);

        $this->hydrate($job, $input);
        if ($job->getLogo() && isset($input['logo'])) {
            $job->setLogo($input['logo']);
        }

        $repository->getMapper()->update($job);

        $flash->success('Your job has been successfully updated');

        return new RedirectResponse($router->generate('job_preview', compact('token')));
    }

    /**
     * Delete an entity
     *
     * @param string $token
     * @param JobRepository $repository
     * @param SessionFlash $flash
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function delete(string $token, JobRepository $repository, SessionFlash $flash, RouterInterface $router)
    {
        $repository->getMapper()
            ->query()
            ->where(['token' => $token])
            ->delete();

        $flash->success('Job successfully deleted');

        return new RedirectResponse($router->generate('jobeet_home'));
    }

    /**
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
            //'logo' => 'required|uploaded_file|max:500K|mimes:jpeg,png',
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