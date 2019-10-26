<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 13:43
 */

namespace App\BankuModule\Controller;

use App\BankuModule\Entity\Account;
use Simplex\DataMapper\EntityManager;
use Simplex\Renderer\TwigRenderer;

class AccountController
{

    /**
     * @var TwigRenderer
     */
    private $view;

    public function __construct(TwigRenderer $renderer)
    {
        $this->view = $renderer;
    }

    public function all(EntityManager $manager)
    {
        $mapper = $manager->getMapper(Account::class);

        return $this->view->render('@banku/account/list', [
            'accounts' => $mapper->query()->nativeQuery()->get()
        ]);
    }
}
