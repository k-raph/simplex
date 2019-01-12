<?php

namespace Simplex\Tests\DataMapper\Fixtures\Mapping;

use Simplex\Tests\DataMapper\Fixtures\Entity\User;
use Simplex\DataMapper\Persistence\ArrayPersister;
use Simplex\Tests\DataMapper\Fixtures\Entity\Comment;

return [
    User::class => [
        'persisterClass' => ArrayPersister::class,
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
                'comments' => [
                    'field' => 'id',
                    'target' => Comment::class,
                    'targetField' => 'author_id'
                ]
            ]
        ]
    ]
];
