<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03/02/2019
 * Time: 20:09
 */

return [
    \App\JobeetModule\Entity\Job::class => [
        'table' => 'jobs',
        'repositoryClass' => \App\JobeetModule\Repository\JobRepository::class,
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'company' => [
                'type' => 'string'
            ],
            'type' => [
                'type' => 'string',
            ],
            'url' => [
                'type' => 'string'
            ],
            'logo' => [
                'type' => 'string'
            ],
            'position' => [
                'type' => 'string'
            ],
            'location' => [
                'type' => 'string'
            ],
            'category' => [
                'column' => 'category_id'
            ],
            'description' => [
                'type' => 'string'
            ],
            'application' => [
                'type' => 'string'
            ],
            'public' => [
                'type' => 'bool',
                'column' => 'is_public'
            ],
            'email' => [
                'type' => 'string'
            ],
            'token' => [
                'type' => 'string'
            ],
            'createdAt' => [
                'type' => 'datetime',
                'column' => 'created_at'
            ],
            'expiresAt' => [
                'type' => 'datetime',
                'column' => 'expires_at'
            ]
        ]
    ]
];