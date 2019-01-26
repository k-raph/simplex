<?php

use App\Blog\Entity\Post;
use App\Blog\Entity\User;

return [
    Post::class => [
        'table' => 'posts',
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'title' => [
                'type' => 'string',
            ],
            'slug' => [
                'type' => 'string'
            ],
            'content' => [
                'type' => 'string'
            ],
            'author' => [
                'column' => 'author_id'
            ]
        ],
        'relations' => [
            'manyToOne' => [
                'author' => [
                    'target' => User::class,
                    'targetField' => 'id'
                ]
            ]
        ]
    ]
];
