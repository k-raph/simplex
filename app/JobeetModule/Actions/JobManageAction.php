<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 00:56
 */

namespace App\JobeetModule\Actions;


use App\JobeetModule\Entity\Job;
use App\JobeetModule\Repository\JobRepository;
use Simplex\Http\Session\SessionFlash;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class JobManageAction
{

    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * JobManageAction constructor.
     * @param TwigRenderer $view
     */
    public function __construct(TwigRenderer $view)
    {
        $this->view = $view;
    }

    /**
     * @param string $token
     * @param JobRepository $repository
     * @return string
     */
    public function preview(string $token, JobRepository $repository)
    {
        $job = $repository->findByToken($token);

        return $this->view->render('@jobeet/job/preview', compact('job'));
    }

    /**
     * @param string $token
     * @param JobRepository $repository
     * @param RouterInterface $router
     * @param SessionFlash $flash
     * @return RedirectResponse
     * @throws \Exception
     */
    public function publish(string $token, JobRepository $repository, RouterInterface $router, SessionFlash $flash)
    {
        /** @var Job $job */
        $job = $repository->findByToken($token, false);

        $job->setExpiresAt((new \DateTime())->add(new \DateInterval('P30D')));
        $repository->getMapper()->update($job);

        $flash->success('Your job has been successfully published');
        return new RedirectResponse($router->generate('job_preview', ['token' => $job->getToken()]));
    }

}