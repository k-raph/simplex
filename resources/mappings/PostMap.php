<?php

use App\Blog\Entity\Post;

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
                'type' => 'int',
                'column' => 'author_id'
            ]
        ]
    ]
];
