<?php

namespace Simplex\Tests\DataMapper\Fixtures\Mapping;

use Keiryo\DataMapper\Persistence\ArrayPersister;
use Simplex\Tests\DataMapper\Fixtures\Entity\Comment;

return [
    Comment::class => [
        'persisterClass' => ArrayPersister::class,
        'table' => 'comments',
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'content'
        ]
    ]
];
