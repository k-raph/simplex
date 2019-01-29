<?php

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\User;

return [
    User::class => [
        'table' => 'users',
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'name' => [
                'type' => 'string',
                'column' => 'username'
            ],
            'email' => [
                'type' => 'string'
            ],
            'password' => [
                'type' => 'string'
            ]
        ],
        'relations' => [
            'oneToMany' => [
                'posts' => [
                    'field' => 'id',
                    'target' => Post::class,
                    'targetField' => 'author_id'
                ],
                'comments' => [
                    'field' => 'id',
                    'target' => Comment::class,
                    'targetField' => 'author_id'
                ]
            ]
        ]
    ]
];
