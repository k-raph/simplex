<?php

use App\Blog\Entity\Post;

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
        ]
    ]
];
