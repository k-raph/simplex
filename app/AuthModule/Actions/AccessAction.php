<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 04:52
 */

namespace App\AuthModule\Actions;


use Simplex\Http\Session\SessionFlash;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use Simplex\Security\Authentication\AuthenticationManager;
use Simplex\Validation\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AccessAction
{

    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var AuthenticationManager
     */
    private $auth;

    /**
     * AccessAction constructor.
     *
     * @param TwigRenderer $view
     * @param Validator $validator
     * @param AuthenticationManager $auth
     */
    public function __construct(TwigRenderer $view, Validator $validator, AuthenticationManager $auth)
    {
        $this->view = $view;
        $this->validator = $validator;
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function loginForm(Request $request)
    {
        if ($this->auth->authenticate($request)) {
            return new RedirectResponse('/');
        }

        return $this->view->render('@auth/login');
    }

    /**
     * @param Request $request
     * @param SessionFlash $flash
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function attemptLogin(Request $request, SessionFlash $flash, RouterInterface $router)
    {
        $credentials = $this->validate($request->request->all())->getValidData();
        $remember = in_array($credentials['remember'], ['on', 1]);

        if ($this->auth->login($credentials, $remember)) {
            $flash->info("You've been successfully logged on");
            $path = $request->getSession()->get('auth.referer', '/');
            $request->getSession()->remove('auth.referer');

            return new RedirectResponse($path, 301);
        }

        $flash->error("Invalid credentials");
        return new RedirectResponse($router->generate('auth_login'));
    }

    /**
     * @param array $input
     * @return \Rakit\Validation\Validation
     */
    private function validate(array $input)
    {
        return $this->validator
            ->validate($input, [
                'login' => 'required',
                'password' => 'required',
                'remember' => 'default:off|in:on,off'
            ]);
    }

    /**
     * @param SessionFlash $flash
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function logout(SessionFlash $flash, RouterInterface $router)
    {
        $this->auth->logout();
        $flash->success("You've successfully been logged out");

        return new RedirectResponse($router->generate('auth_login'));
    }
}