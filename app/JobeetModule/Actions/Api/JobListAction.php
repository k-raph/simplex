<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31/03/2019
 * Time: 10:42
 */

namespace App\JobeetModule\Actions\Api;

use App\JobeetModule\Entity\Job;
use App\JobeetModule\Repository\JobRepository;
use Simplex\Routing\RouterInterface;
use Simplex\Security\Authentication\StatelessAuthenticationManager as AuthManager;
use Symfony\Component\HttpFoundation\Request;

class JobListAction
{

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AuthManager
     */
    private $auth;

    /**
     * JobListAction constructor.
     * @param RouterInterface $router
     * @param AuthManager $auth
     */
    public function __construct(RouterInterface $router, AuthManager $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

    /**
     * Get all jobs for api
     *
     * @param Request $request
     * @param JobRepository $repository
     * @return array
     */
    public function all(Request $request, JobRepository $repository): array
    {
        $categories = [];
        $limit = null;
        if ($request->isMethod('POST')) {
            $categories = $request->request->get('categories', []);
            $limit = $request->request->get('max');
        }

        $jobs = $repository->getActiveForAffiliate($this->auth->getUser()->getId(), $categories, $limit);
        $jobs = array_map(function (array $job) {
            $job['type'] = Job::TYPES[$job['type']];

            $options = [
                'id' => $this->slugify($job['id']),
                'company' => $this->slugify($job['company']),
                'location' => $this->slugify($job['location']),
                'position' => $this->slugify($job['position'])
            ];

            unset($job['id']);
            $job['link'] = $this->router->generate('job_show', $options);

            return $job;
        }, $jobs);

        return $jobs;
    }

    /**
     * Slugify a word
     *
     * @param string $string
     * @return string
     */
    private function slugify(string $string): string
    {
        $string = preg_replace('~\W+~', '-', $string);
        $string = trim($string, '-');

        return strtolower($string);
    }
}
