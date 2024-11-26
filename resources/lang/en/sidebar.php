<?php 
    return [
        'module' => [
            [
                'title' => 'Artical Managment',
                'icon' => 'fa fa-file',
                'name' => ['post'],
                'subModule' => [
                    [
                        'title' => 'Artical Group',
                        'route' => 'post.catalogue.index'
                    ],
                    [
                        'title' => 'Artical',
                        'route' => 'post.index'
                    ],
                ]
            ],
            [
                'title' => 'User Group Management',
                'icon' => 'fa fa-user',
                'name' => ['user'],
                'subModule' => [
                    [
                        'title' => "User Group",
                        'route' => "user.catalogue.index"
                    ],
                    [
                        'title' => 'User',
                        'route' => "user.index"
                    ],
                    [
                        'title' => 'Permission',
                        'route' => "permission.index"
                    ]
                ]
            ],
            [
                'title' => 'General Configuration',
                'icon' => 'fa fa-file',
                'name' => ['language'],
                'subModule' => [
                    [
                        'title' => 'Language Management',
                        'route' => 'language.index'
                    ],
                ]
            ]
        ]
    ];