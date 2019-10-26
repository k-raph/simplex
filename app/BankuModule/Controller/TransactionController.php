<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 13:44
 */

namespace App\BankuModule\Controller;

use Simplex\Renderer\TwigRenderer;

class TransactionController
{

    /**
     * @var TwigRenderer
     */
    private $view;

    public function __construct(TwigRenderer $renderer)
    {
        $this->view = $renderer;
    }

    public function new()
    {
        return $this->view->render('@banku/transaction/new');
    }

    public function execute()
    {
    }
}
