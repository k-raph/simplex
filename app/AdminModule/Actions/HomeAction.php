<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 15:47
 */

namespace App\AdminModule\Actions;


use Simplex\Renderer\TwigRenderer;

class HomeAction
{

    public function panel(TwigRenderer $renderer)
    {
        return $renderer->render('@admin/panel');
    }

}