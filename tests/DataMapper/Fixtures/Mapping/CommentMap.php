<?php

namespace Simplex\Tests\DataMapper\Fixtures\Mapping;

use Simplex\Tests\DataMapper\Fixtures\Entity\Comment;
use Simplex\DataMapper\Persistence\ArrayPersister;
use Simplex\Tests\DataMapper\Fixtures\Entity\User;

return [
    Comment::class => [
        'persisterClass' => ArrayPersister::class,
        'table' => 'comments',
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'content' => [
                'type' => 'string',
                'column' => 'username'
            ],
        ],
        'relations' => [
            'manyToOne' => [
                'author' => [
                    'field' => 'author_id',
                    'target' => User::class,
                    'targetField' => 'id'
                ]
            ]
        ]
    ]
];
