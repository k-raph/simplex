<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 10:27
 */

namespace App\JobeetModule\Actions;


use App\JobeetModule\Entity\Affiliate;
use Simplex\Database\Query\Builder;
use Simplex\DataMapper\EntityManager;
use Simplex\Helper\ValidatorTrait;
use Simplex\Renderer\TwigRenderer;
use Simplex\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AffiliateRegisterAction
{

    use ValidatorTrait;

    /**
     * @var array|array[]
     */
    protected $categories;

    /**
     * AffiliateRegisterAction constructor.
     * @param Builder $builder
     * @throws \Throwable
     */
    public function __construct(Builder $builder)
    {
        $this->categories = $builder->table('categories')
            ->addSelect('id', 'slug')
            ->addSelect('name')
            ->get();
    }

    /**
     * Renders registration form
     *
     * @param TwigRenderer $renderer
     * @return string
     */
    public function form(TwigRenderer $renderer)
    {
        return $renderer->render('@jobeet/affiliate/new', ['categories' => $this->categories]);
    }

    /**
     * Performs registration request persistence
     *
     * @param Request $request
     * @param EntityManager $manager
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function request(Request $request, EntityManager $manager, RouterInterface $router)
    {
        $input = $this->validate($request->request->all())->getValidData();

        $affiliate = new Affiliate();
        $affiliate->setName($input['name']);
        $affiliate->setEmail($input['email']);
        $affiliate->setUrl($input['url']);
        $affiliate->setActive(false);
        foreach ($input['categories'] ?? [] as $category) {
            $affiliate->addCategory($category);
        }

        $manager->getMapper(Affiliate::class)->insert($affiliate);

        return new RedirectResponse($router->generate('affiliate_wait'));
    }

    /**
     * Displays Succeed registration page
     *
     * @param TwigRenderer $renderer
     * @return string
     */
    public function success(TwigRenderer $renderer)
    {
        return $renderer->render('@jobeet/affiliate/success');
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

        return [
            'name' => 'required|alpha_spaces',
            'email' => 'required|email',
            'url' => 'required|url',
            'categories' => "array:$categories"
        ];
    }
}