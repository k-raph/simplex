<?php

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\User;

return [
    Post::class => [
        'table' => 'posts',
        'repositoryClass' => \App\Blog\Repository\PostRepository::class,
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
            ],
            'oneToMany' => [
                'comments' => [
                    'field' => 'id',
                    'target' => Comment::class,
                    'targetField' => 'post_id'
                ]
            ]
        ]
    ]
];
