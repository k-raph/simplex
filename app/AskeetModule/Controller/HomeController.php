<?php
/**
 * Created by PhpStorm.
 * User: K. Raph
 * Date: 30/10/2019
 * Time: 15:03
 */

namespace App\AskeetModule\Controller;

use App\AskeetModule\Repository\QuestionRepository;
use Simplex\Renderer\TwigRenderer;

class HomeController
{

    public function index(QuestionRepository $questions, TwigRenderer $view)
    {
        return $view->render('@askeet/home', ['questions' => $questions->getAll()]);
    }

}