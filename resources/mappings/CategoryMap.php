<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:24
 */

return [
    \App\JobeetModule\Entity\Category::class => [
        'table' => 'categories',
        'repositoryClass' => \App\JobeetModule\Repository\CategoryRepository::class,
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'name' => [
                'type' => 'string'
            ],
            'slug' => [
                'type' => 'string'
            ]
        ]
    ]
];