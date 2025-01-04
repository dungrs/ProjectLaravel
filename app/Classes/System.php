<?php
namespace App\Classes;

class System {
    public function construct () {

    }

    public function config() {
        $data['homepage'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin chung của websit. Tên thương hiệu website, Logo, Favicon, vv ...',
            'value' => [
                'company' => ['type' => 'text', 'label' => 'Tên công ty'],
                'brand' => ['type' => 'text', 'label' => 'Tên thương hiệu'],
                'slogan' => ['type' => 'text', 'label' => 'Slogan'],
                'logo' => ['type' => 'image', 'label' => 'Logo Website', 'title' => 'Click vào ô dưới để tải logo'],
                'favicon' => ['type' => 'image', 'label' => 'Favicon', 'title' => 'Click vào ô dưới để tải logo'],
                'copyright' => ['type' => 'text', 'label' => 'Copyright'],
                'website_status' => [
                    'type' => 'select',
                    'label' => 'Tình trạng website',
                    'option' => [
                        'open' => 'Mở cửa website',
                        'close' => 'Website đang bảo trì'
                    ]
                ],
                'short_info' => ['type' => 'editor', 'label' => 'Giới thiệu ngắn'],
            ],
        ];

        $data['contact'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin liên hệ website ví dụ: Địa chỉ công ty, Văn phòng giao dịch, Hotline, Bản đồ, vv...',
            'value' => [
                'office' => ['type' => 'text', 'label' => 'Địa chỉ'],
                'address' => ['type' => 'text', 'label' => 'Văn phòng giao dịch'],
                'hotline' => ['type' => 'text', 'label' => 'Hotline'],
                'technical_phone' => ['type' => 'text', 'label' => 'Hotline kỹ thuật'],
                'sell_phone' => ['type' => 'text', 'label' => 'Hotline kinh doanh'],
                'phone' => ['type' => 'text', 'label' => 'Số cố định'],
                'fax' => ['type' => 'text', 'label' => 'Fax'],
                'email' => ['type' => 'text', 'label' => 'Mã số thuế'],
                'website' => ['type' => 'text', 'label' => 'Website'],
                'map' => [
                    'type' => 'textarea', 
                    'label' => 'Bản đồ', 
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => 'https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/',
                        'target' => '_blank'
                    ]
                ],
            ],
        ];

        $data['seo'] = [
            'label' => 'Cấu hình Seo dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm Tiêu dề SEO, Từ khóa SEO, Mô tả SEO, Meta Images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description' => ['type' => 'text', 'label' => 'Mô tả SEO'],
                'meta_image' => ['type' => 'image', 'label' => 'Ảnh'],
            ],
        ];

        $data['social'] = [
            'label' => 'Cấu hình Mạng xã hội dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về Mạng xã hội của trang chủ website',
            'value' => [
                'facebook' => ['type' => 'text', 'label' => 'Facebook'],
                'youtube' => ['type' => 'text', 'label' => 'Youtube'],
                'twitter' => ['type' => 'text', 'label' => 'Twitter'],
                'tiktok' => ['type' => 'text', 'label' => 'Tiktok'],
                'instagram' => ['type' => 'text', 'label' => 'Instagram'],
            ],
        ];

        return $data;
    }
}