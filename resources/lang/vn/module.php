<?php 
    return [
        'model' => [
            'PostCatalogue' => 'Nhóm Bài Viết',
            "Post" => 'Bài Viết',
            'ProductCatalogue' => 'Nhóm Sản Phẩm',
            'Product' => "Sản Phẩm"
        ],
        'type' => [
            'dropdown_menu' => 'Drop Menu',
            'mega_menu' => 'Mega Menu'
        ],
        'effect' => [
            'fade' => 'Fade',
            'cube' => 'Cube',
            'coverFlow' => 'CoverFlow',
            'flip' => 'Flip',
            'cards' => 'Cards',
            'creative' => 'Creative'
        ],
        'navigate' => [
            'hide' => "Ẩn",
            'dots' => "Dấu chấm",
            'thumbnails' => "Ảnh Thumbnails"
        ],
        'promotion' => [
            'order_amount_range' => 'Chiết khấu theo tổng giá trị đơn hàng',
            'product_and_quantity' => 'Chiết khấu theo từng sản phẩm',
            'product_quantity_range' => 'Chiết khấu theo số lượng sản phẩm',
            'goods_discount_by_quantity' => 'Mua sản phẩm - giảm giá sản phẩm'
        ],
        'item' => [
            'Product' => 'Phiên bản sản phẩm',
            'ProductCatalogue' => 'Loại sản phẩm',
        ],
        'gender' => [
            [
                'id' => 1,
                'name' => 'Nam'
            ],
            [
                'id' => 2,
                'name' => 'Nữ'
            ]
        ],
        // Tạo ra 1 mảng như này 
        // [0 => 1], [1 =>2]
        'day' => array_map(function($value) {
            return ['id' => $value - 1, 'name' => $value];
        }, range(1, 31))
    ];

