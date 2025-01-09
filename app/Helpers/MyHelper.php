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

if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        // Đảm bảo giá trị là số
        $price = is_numeric($price) ? $price : 0;

        // Định dạng số với dấu phẩy hàng nghìn và thêm ký tự "đ"
        return number_format($price, 0, '.', '.') . ' đ';
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

if (!function_exists('loadClass')) {
    function loadClass(string $model = '', string $folder = 'Repositories',  $interface = 'Repository') {
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . ucfirst($model) . $interface;
        if (!class_exists($serviceInterfaceNamespace)) {
            return response()->json(['error' => 'Repository not found.'], 404);
        }
        
        $serviceInterface = app($serviceInterfaceNamespace);

        return $serviceInterface;
    }
}

if (!function_exists('writeUrl')) {
    function writeUrl(string $canonical, bool $fullDomain = true, $suffix = false): string {
        // If the canonical already contains a protocol (http/https), return it as is.
        if (filter_var($canonical, FILTER_VALIDATE_URL)) {
            return $canonical;
        }

        $fileUrl = ($fullDomain ? config('app.url') : '') . $canonical;

        // Append the suffix if needed.x
        if ($suffix !== false) {
            $fileUrl .= $suffix === true ? config('apps.general.suffix') : '';
        }

        return $fileUrl;
    }
}

if (!function_exists('frontendRecursiveMenu')) {
    function frontendRecursiveMenu($datas, $level = 1) {
        if (empty($datas)) {
            return '';
        }

        $html = '';

        foreach($datas as $menuNode) {
            $menu = $menuNode['item'];
            $name = $menu->name;
            $canonical = writeUrl($menu->canonical, true, true);

            $html .= '<li class="' . (($level == 1) ? 'children' : '') . '">';
            $html .= '<a href="' . $canonical . '" title="">'. $name .'</a>';
            if ($level === 1) {
                $html .= '<div class="dropdown-menu">';
            }
            if (!empty($menuNode['children'])) {
                $menuHtml = '';
                $menuHtml .= '<ul class="uk-list uk-clearfix menu-style menu-level__' . ($level + 1) . '">';
                $menuHtml .= frontendRecursiveMenu($menuNode['children'], $level + 1);
                $menuHtml .= "</ul>";
                $html .= $menuHtml;
            }
            if ($level === 1) {
                $html .= '</div>';
            }

            $html .= '</li>';

        }

        return $html;
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

if (!function_exists('image')) {
    function image(string $image = '')
    {
        return $image;
    }
}

if (!function_exists('getReview')) {
    function getReview(string $review = '')
    {
        return [
            'stars' => rand(1, 4),
            'counts' => rand(0, 100)
        ];
    }
}

if (!function_exists('getDiscount')) {
    function getDiscountType($promotion)
    {
        $discount = [
            'type' => $promotion['discountType'] === 'percent' ? '%' : 'đ',
            'value' => number_format($promotion['discountValue']),
            'old_price' => number_format($promotion['product_price']),
            'sale_price' => number_format($promotion['product_price'] - $promotion['finalDiscount'])
        ];
        return $discount;
    }
}

if (!function_exists('convertDateTime')) {
    function convertDateTime($dateTime)
    {
        return \Carbon\Carbon::parse($dateTime)->format('d/m/Y');
    }
}

if (!function_exists('cutnchar')) {
    function cutnchar($str = null, $n = 200)
    {
        if (strlen($str) < $n) return $str;
        $html = substr($str, 0, $n);
        $html = substr($html, 0, strrpos($html, '' ));
        return $str;
    }
}

if (!function_exists('cutStringAndDecode')) {
    function cutStringAndDecode($str = null, $n = 200)
    {
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = cutnchar($str, $n);
        return $str;
    }
}

if (!function_exists('seo')) {
    function seo($model, $page = 1)
    {       
        $canonical = ($page > 1) ? writeUrl($model->canonical, true, false) . '/trang-' . $page . config('apps.general.suffix') : writeUrl($model->canonical, true, true);
        return [
            'meta_title' => (!empty($model->meta_title)) ? $model->meta_title : $model->name,
            'meta_keyword' => (!empty($model->meta_keyword)) ? $model->meta_keyword : $model->keyword,
            'meta_description' => (!empty($model->meta_description)) ? $model->meta_description : cutStringAndDecode($model->description, 168),
            'meta_image' => $model->image,
            'canonical' => $canonical,
        ];
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
                $discountValue = $promotion->discountValue ?? 'Không xác định';
                $discountType = $promotion->discountType === 'percent' ? '%' : 'đ';

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

if (!function_exists('renderQuickBuy')) {
    function renderQuickBuy($product, $name, $canonical)
    {   
        $class = 'btn-addCart';
        $openModal = '';
        if (isset($product->product_variants) && count($product->product_variants)) {
            $class = '';
            $canonical = '#popup';
            $openModal = 'data-uk-modal';
        }
        $html = <<<HTML
            <a href="{ asset($canonical) }" { $openModal } title="{ $name }" class="{ $class }">
                <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g>
                    <path d="M24.4941 3.36652H4.73614L4.69414 3.01552C4.60819 2.28593 4.25753 1.61325 3.70863 1.12499C3.15974 0.636739 2.45077 0.366858 1.71614 0.366516L0.494141 0.366516V2.36652H1.71614C1.96107 2.36655 2.19748 2.45647 2.38051 2.61923C2.56355 2.78199 2.68048 3.00626 2.70914 3.24952L4.29414 16.7175C4.38009 17.4471 4.73076 18.1198 5.27965 18.608C5.82855 19.0963 6.53751 19.3662 7.27214 19.3665H20.4941V17.3665H7.27214C7.02705 17.3665 6.79052 17.2764 6.60747 17.1134C6.42441 16.9505 6.30757 16.7259 6.27914 16.4825L6.14814 15.3665H22.3301L24.4941 3.36652ZM20.6581 13.3665H5.91314L4.97214 5.36652H22.1011L20.6581 13.3665Z" fill="#253D4E"></path>
                    <path d="M7.49414 24.3665C8.59871 24.3665 9.49414 23.4711 9.49414 22.3665C9.49414 21.2619 8.59871 20.3665 7.49414 20.3665C6.38957 20.3665 5.49414 21.2619 5.49414 22.3665C5.49414 23.4711 6.38957 24.3665 7.49414 24.3665Z" fill="#253D4E"></path>
                    <path d="M17.4941 24.3665C18.5987 24.3665 19.4941 23.4711 19.4941 22.3665C19.4941 21.2619 18.5987 20.3665 17.4941 20.3665C16.3896 20.3665 15.4941 21.2619 15.4941 22.3665C15.4941 23.4711 16.3896 24.3665 17.4941 24.3665Z" fill="#253D4E"></path>
                    </g>
                    <defs>
                    <clipPath>
                    <rect width="24" height="24" fill="white" transform="translate(0.494141 0.366516)"></rect>
                    </clipPath>
                    </defs>
                </svg>
            </a>
        HTML;

        return $html;
    }
}

if (!function_exists('categorySelectRaw')) {
    function categorySelectRaw($table = 'product')
    {
        $table_plural = $table . 's';
        $catalogue_table = $table . '_catalogues';
        $pivot_table = $table . '_catalogue_' . $table;

        return "
            (
                SELECT COUNT(DISTINCT items.id)
                FROM {$table_plural} AS items
                JOIN {$pivot_table} AS pivot ON pivot.{$table}_id = items.id
                WHERE pivot.{$table}_catalogue_id IN (
                    SELECT sub_catalogue.id
                    FROM {$catalogue_table} AS sub_catalogue
                    WHERE sub_catalogue.lft >= (
                        SELECT parent.lft
                        FROM {$catalogue_table} AS parent
                        WHERE parent.id = main_catalogue.id
                    )
                    AND sub_catalogue.rgt <= (
                        SELECT parent.rgt
                        FROM {$catalogue_table} AS parent
                        WHERE parent.id = main_catalogue.id
                    )
                )
            ) AS product_count
        ";
    }
}

if (!function_exists('sortString')) {
    function sortString($string = '')
    {
        $extract = explode(',', $string);
        sort($extract, SORT_NUMERIC);
        $newArray = implode(',', $extract);
        return $newArray;
    }
}

if (!function_exists('sortAttributeId')) {
    function sortAttributeId($attributeId = [])
    {
        sort($attributeId); // Sắp xếp mảng để đảm bảo tính nhất quán
        $attributeString = implode(', ', array_map('trim', $attributeId));
        return $attributeString;
    }
}

