<?php
/**
 * Created by PhpStorm.
 * User: K. Raph
 * Date: 30/10/2019
 * Time: 18:12
 */

namespace App\AskeetModule\Controller;


use App\AskeetModule\Repository\QuestionRepository;
use Simplex\Renderer\TwigRenderer;

class QuestionController
{

    /**
     * @var TwigRenderer
     */
    private $view;

    /**
     * QuestionController constructor.
     * @param TwigRenderer $view
     */
    public function __construct(TwigRenderer $view)
    {

        $this->view = $view;
    }

    public function single(string $slug, QuestionRepository $questions, AnswerRepository $answers)
    {
        $question = $questions->find($slug, 'slug');
        $question['answers'] = $answers->findForQuestion($question);

        return $this->view->render('@askeet/question_single', compact('question'));
    }

}