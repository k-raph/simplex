<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07/02/2019
 * Time: 10:28
 */

use App\JobeetModule\Entity\Affiliate;

return [
    Affiliate::class => [
        'table' => 'affiliates',
        'repositoryClass' => \App\JobeetModule\Repository\AffiliateRepository::class,
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
            'url' => [
                'type' => 'string'
            ],
            'token' => [
                'type' => 'string'
            ],
            'active' => [
                'type' => 'boolean',
                'column' => 'is_active'
            ]
        ]
    ]
];