<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 15:43
 */

namespace App\JobeetModule\Admin\Actions;

use App\JobeetModule\Actions\AffiliateRegisterAction;
use App\JobeetModule\Admin\Events\AffiliateActivationEvent;
use App\JobeetModule\Admin\Repository\AffiliateRepository;
use App\JobeetModule\Entity\Affiliate;
use App\JobeetModule\Mapper\AffiliateMapper;
use Keiryo\Database\Query\Builder;
use Keiryo\DataMapper\EntityManager;
use Keiryo\EventManager\EventManagerInterface;
use Keiryo\Helper\Str;
use Keiryo\Renderer\Twig\TwigRenderer;
use Keiryo\Routing\RouterInterface;
use Simplex\Http\Session\SessionFlash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AffiliateManageAction extends AffiliateRegisterAction
{

    /**
     * @var TwigRenderer
     */
    private $view;

    public function __construct(Builder $builder, TwigRenderer $view)
    {
        parent::__construct($builder);
        $this->view = $view;
    }

    /**
     * Show all affiliates
     *
     * @param AffiliateRepository $repository
     * @return string
     */
    public function list(AffiliateRepository $repository)
    {
        return $this->view->render('@jobeet/admin/affiliates', [
            'affiliates' => $repository->findAll()
        ]);
    }

    /**
     * Activate an affiliate account
     *
     * @param int $id
     * @param EntityManager $manager
     * @param EventManagerInterface $events
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return RedirectResponse
     * @throws \Exception
     */
    public function activate(
        int $id,
        EntityManager $manager,
        EventManagerInterface $events,
        RouterInterface $router,
        SessionFlash $flash
    )
    {
        /** @var Affiliate $affiliate */
        $affiliate = $manager->find(Affiliate::class, $id);
        $affiliate->setActive(!$affiliate->isActive());

        if ($affiliate->isActive() && !$affiliate->getToken()) {
            $token = Str::random(32);
            $affiliate->setToken($token);

            $events->dispatch(new AffiliateActivationEvent($affiliate));
        }

        $manager->persist($affiliate);
        $manager->flush();

        if ($affiliate->isActive()) {
            $flash->success('Affiliate successfully activated');
        } else {
            $flash->error('Affiliate successfully deactivated');
        }

        return new RedirectResponse($router->generate('admin_jobeet_home'));
    }

    /**
     * @param Request $request
     * @param EntityManager $manager
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return string
     */
    public function edit(Request $request, EntityManager $manager, RouterInterface $router, SessionFlash $flash)
    {
        $id = $request->attributes->get('_route_params')['id'];
        /** @var Affiliate $affiliate */
        $affiliate = $manager->find(Affiliate::class, $id);
        if ('POST' === $request->getMethod()) {
            $data = $this->validate($request->request->all())->getValidData();

            $affiliate->setName($data['name']);
            $affiliate->setUrl($data['url']);
            $affiliate->setEmail($data['email']);
            $affiliate->setCategories($data['categories']);

            $manager->persist($affiliate);
            $manager->flush();

            $flash->success('Affiliate successfully edited');
            return new RedirectResponse($router->generate('admin_jobeet_home'));
        }

        return $this->view->render('@jobeet/admin/affiliate_edit', [
            'affiliate' => $affiliate,
            'categories' => $this->categories
        ]);
    }

    /**
     * @param int $id
     * @param AffiliateMapper $mapper
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return RedirectResponse
     */
    public function delete(int $id, AffiliateMapper $mapper, RouterInterface $router, SessionFlash $flash)
    {
        $success = $mapper->query()
            ->where('id', $id)
            ->delete();

        if ($success) {
            $flash->success('Affiliate successfully deleted');
        } else {
            $flash->error('An error occured!!');
        }

        return new RedirectResponse($router->generate('admin_jobeet_home'));
    }

    /**
     * Create an affiliate
     *
     * @param Request $request
     * @param EntityManager $manager
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return string|RedirectResponse
     */
    public function create(Request $request, EntityManager $manager, RouterInterface $router, SessionFlash $flash)
    {
        if ($request->isMethod('POST')) {
            // Call register controller and process response
            $response = $this->request($request, $manager, $router);
            if ($response instanceof RedirectResponse) {
                $flash->success('Affiliate successfully edited');
                return new RedirectResponse($router->generate('admin_jobeet_home'));
            }
        }

        return $this->view->render('@jobeet/admin/affiliate_new', [
            'categories' => $this->categories
        ]);
    }
}
