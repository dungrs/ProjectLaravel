<?php
if (!function_exists('convert_array')) {
    function convert_array($system = null, $keyword = '', $value = '') {
        $temp = [];
        if (is_array($system)) {
            foreach($system as $key => $val) {
                $temp[$keyword] = $val[$value];
            }
        }

        if (is_object($system)) {
            foreach($system as $key => $val) {
                $temp[$val->$keyword] = $val->{$value};
            }
        }

        return $temp;
    }
}

if (!function_exists('renderSystemInput')) {
    function renderSystemInput(string $name = '', $systems = null) {
        return '<input type="text" name="config['. $name .']" value="'. old($name, isset($systems[$name]) ? $systems[$name] : '' ) .'" class="form-control" placeholder="" autocomplete="off" >';
    }
}

if (!function_exists('renderSystemImage')) {
    function renderSystemImage(string $name = '', $systems = null) {
        return '<input type="text" name="config['. $name .']" value="'. old($name, isset($systems[$name]) ? $systems[$name] : '' ) .'" class="form-control upload-image" placeholder="" autocomplete="off" >';
    }
}

if (!function_exists('renderSystemTextarea')) {
    function renderSystemTextarea(string $name = '', $systems = null) {
        $map = old($name, isset($systems[$name]) ? $systems[$name] : '' );
        return <<<TEXTAREA
            '<textarea name="config[$name]" value="" class="form-control system-textarea">$map
            </textarea>'
        TEXTAREA;
    }
}

if (!function_exists('renderSystemEditor')) {
    function renderSystemEditor(string $name = '', $systems = null) {
        $map = old($name, isset($systems[$name]) ? $systems[$name] : '' );
        return <<<TEXTAREA
            '<textarea name="config[$name]" id="$name" class="form-control system-textarea ck-editor">$map
            </textarea>'
        TEXTAREA;
    }
}

if (!function_exists('renderSystemLink')) {
    function renderSystemLink(array $item = [], $systems = null) {
        return isset($item['link']) ? '<a class="system-link" target="'.$item['link']['target'].'" href="'.$item['link']['href'].'">'.$item['link']['text'].'</a>' : '';
    }
}

if (!function_exists('renderSystemTitle')) {
    function renderSystemTitle(array $item = [], $systems = null) {
        return isset($item['title']) ? '<span class="system-link text-danger">'.$item['title'].'</span>' : '';
    }
}

if (!function_exists('renderSystemSelect')) {
    function renderSystemSelect(array $item = [], string $name = '', $systems = null) {
        $option = '';
        foreach($item['option'] as $key => $val) {
            $option .= '<option '. ((isset($systems[$name]) && $systems[$name] == $key) ? 'selected' : "") .' value="'. $key .'">'. $val .'</option>';
        }
        $html = <<<SELECT
            <select name="config[$name]" class="form-control">
                $option
            </select>
        SELECT;

        return $html;
    }
}

if (!function_exists('recursive')) {
    function recursive($datas = [], $parentId = 0, $level = 1, $maxDepth = 5) {
        $result = [];
        if ($level > $maxDepth) {
            return $result;
        }

        foreach($datas as $data) {
            if ($data->parent_id == $parentId) {
                $currentItem = [
                    'item' => $data,
                    'children' => recursive($datas, $data->id, $level + 1, $maxDepth)
                ];
                $result[] = $currentItem;
            }
        }

        return $result;
    }
}

if (!function_exists('recursiveMenuHtml')) {
    function recursiveMenuHtml($datas, $level = 1) {
        if (empty($datas)) {
            return '';
        }

        $html = '<ol class="dd-list">';
        foreach ($datas as $menuNode) {
            $menu = $menuNode['item'];

            // Tạo URL route
            $route = route('menu.children', ['parent_id' => $menu->id]);

            // Use heredoc for HTML rendering
            $menuHtml = <<<HTML
            <li class="dd-item" data-id="{$menu->id}">
                <div class="dd-handle">
                    <span class="label label-info"><i class="fa fa-arrows"></i></span> 
                    {$menu->name}
                </div>
                <a href="{$route}" class="create-children-menu">Quản lý menu con</a>
            HTML;

            // Nếu có children, gọi đệ quy để hiển thị các menu con
            if (!empty($menuNode['children'])) {
                $menuHtml .= recursiveMenuHtml($menuNode['children'], $level + 1);
            }

            $menuHtml .= '</li>';
            $html .= $menuHtml;
        }
        $html .= '</ol>';

        return $html;
    }
}


if (!function_exists('buildMenu')) {
    function buildMenu($menus = null, $parent_id = 0, $prefix = '') {
        $output = [];
        $count = 1;
        
        if (count($menus)) {
            foreach($menus as $menu) {
                if ($menu->parent_id == $parent_id) {
                    $menu->position = $prefix.$count;
                    $output[] = $menu;
                    $output = array_merge($output, buildMenu($menus, $menu->id, $menu->position . '.'));
                    $count ++;
                }
            }
        }

        return $output;
    }
}

if (!function_exists('convertArray')) {
    function convertArray(array $feilds = [], $data) : array {
        $temp = [];
        foreach ($data as $key => $val) {
            foreach ($feilds as $feild) {
                $temp[$feild][] = $val[$feild];
            }
        }
        return $temp;
    }
}

if (!function_exists('normalizeAmount')) {
    function normalizeAmount($amount)
    {
        return (int) str_replace(',', '', $amount);
    }
}

if (!function_exists('convertDateTime')) {
    function convertDateTime($dateTime)
    {
        return \Carbon\Carbon::parse($dateTime)->format('d/m/Y');
    }
}

if (!function_exists('renderDiscountInformation')) {
    function renderDiscountInformation($promotion)
    {   
        if ($promotion->method === 'product_and_quantity') {
            // Giải mã JSON của discount_information (nếu cần)
            $discountInformation = is_string($promotion->discount_information) 
                ? json_decode($promotion->discount_information, true) 
                : $promotion->discount_information;

            if (is_array($discountInformation) && isset($discountInformation['info'])) {
                $discountValue = $discountInformation['info']['discountValue'] ?? 'Không xác định';
                $discountType = $discountInformation['info']['discountType'] === 'percent' ? '%' : 'đ';

                return <<<HTML
                    <span class="label label-success">{$discountValue} {$discountType}</span>
                HTML;
            }

            return '<div>Dữ liệu khuyến mãi không hợp lệ</div>';
        }

        // Nếu không phải phương thức 'product_and_quantity'
        $route = route("promotion.edit", $promotion->id);
        return <<<HTML
            <div>
                <a href="{$route}">Xem chi tiết</a>
            </div>
        HTML;
    }
}

