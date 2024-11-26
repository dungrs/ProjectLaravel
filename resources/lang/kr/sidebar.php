<?php 
    return [
        'module' => [
            [
                'title' => '기사 관리', // Artical Management
                'icon' => 'fa fa-file',
                'name' => ['post'],
                'subModule' => [
                    [
                        'title' => '기사 그룹', // Artical Group
                        'route' => 'post.catalogue.index'
                    ],
                    [
                        'title' => '기사', // Artical
                        'route' => 'post.index'
                    ],
                ]
            ],
            [
                'title' => '사용자 그룹 관리', // User Group Management
                'icon' => 'fa fa-user',
                'name' => ['user'],
                'subModule' => [
                    [
                        'title' => "사용자 그룹", // User Group
                        'route' => "user.catalogue.index"
                    ],
                    [
                        'title' => '사용자', // User
                        'route' => "user.index"
                    ],
                    [
                        'title' => '허가', // Permission
                        'route' => "user.index"
                    ]
                ]
            ],
            [
                'title' => '일반 설정', // General Configuration
                'icon' => 'fa fa-file',
                'name' => ['language'],
                'subModule' => [
                    [
                        'title' => '언어 관리', // Language Management
                        'route' => 'language.index'
                    ],
                ]
            ]
        ]
    ];
