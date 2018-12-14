<?php

namespace App\Api\Action;

class ShowAction
{

    public function __invoke()
    {
        return [
            'name' => 'John',
            'firstname' => 'Doe',
            'job' => 'student'
        ];
    }
}