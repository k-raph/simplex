<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/01/2019
 * Time: 18:53
 */

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\User;

return [
    Comment::class => [
        'table' => 'comments',
        'repositoryClass' => \App\Blog\Repository\CommentRepository::class,
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'content' => [
                'type' => 'string',
            ],
            'createdAt' => [
                'type' => 'datetime',
                'column' => 'created_at'
            ],
            'post' => [
                'column' => 'post_id'
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
                ],
                'post' => [
                    'target' => Post::class,
                    'targetField' => 'id'
                ]
            ]
        ]
    ]
];
