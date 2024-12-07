<?php 
    return [
        'module' => [
            [
                'title' => 'QL Sản Phẩm',
                'icon' => 'fa fa-cube',
                'name' => ['product', 'attribute'],
                'subModule' => [
                    [
                        'title' => 'QL Nhóm Sản Phẩm',
                        'route' => 'product.catalogue.index'
                    ],
                    [
                        'title' => 'QL Sản Phẩm',
                        'route' => 'product.index'
                    ],
                    [
                        'title' => 'QL Loại thuộc tính',
                        'route' => 'attribute.catalogue.index'
                    ],
                    [
                        'title' => 'QL Thuộc tính',
                        'route' => 'attribute.index'
                    ],
                ]
            ],
            [
                'title' => 'QL Bài Viết',
                'icon' => 'fa fa-file',
                'name' => ['post'],
                'subModule' => [
                    [
                        'title' => 'QL Nhóm Bài Viết',
                        'route' => 'post.catalogue.index'
                    ],
                    [
                        'title' => 'QL Bài Viết',
                        'route' => 'post.index'
                    ],
                ]
            ],
            [
                'title' => 'QL Nhóm Thành Viên',
                'icon' => 'fa fa-user',
                'name' => ['user', 'permission'],
                'subModule' => [
                    [
                        'title' => "QL Nhóm Thành Viên",
                        'route' => "user.catalogue.index"
                    ],
                    [
                        'title' => 'QL Thành Viên',
                        'route' => "user.index"
                    ],
                    [
                        'title' => 'QL Quyền',
                        'route' => "permission.index"
                    ]
                ]
            ],
            [
                'title' => 'QL Banner & Slide',
                'icon' => 'fa fa-picture-o',
                'name' => ['slide'],
                'subModule' => [
                    [
                        'title' => 'Cài đặt Slide',
                        'route' => 'slide.index'
                    ],
                ]
            ],
            [
                'title' => 'QL Menu',
                'icon' => 'fa fa-bars',
                'name' => ['menu'],
                'subModule' => [
                    [
                        'title' => 'Cài đặt Menu',
                        'route' => 'menu.index'
                    ],
                ]
            ],
            [
                'title' => 'Cấu hình chung',
                'icon' => 'fa fa-file',
                'name' => ['language', 'generate', 'system', 'widget'],
                'subModule' => [
                    [
                        'title' => 'QL Ngôn Ngữ',
                        'route' => 'language.index'
                    ],
                    [
                        'title' => 'QL Module',
                        'route' => 'generate.index'
                    ],
                    [
                        'title' => 'Cấu hình hệ thống',
                        'route' => 'system.index'
                    ],
                    [
                        'title' => 'Quản lý Widget',
                        'route' => 'widget.index'
                    ],
                ]
            ]
        ]
    ];