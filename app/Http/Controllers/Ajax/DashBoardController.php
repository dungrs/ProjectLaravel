<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Language;

use Illuminate\Support\Str;

class DashBoardController extends Controller
{   
    protected $language;

    public function __construct() {
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới cấc middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }

    public function changeStatus(Request $request) {
        $post = $request->input();
        // Khởi tạo import
        $serviceInterface = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterface)) {
            // Sử dụng đường dẫn 
            $serviceInterface = app($serviceInterface);
        }
        $flag = $serviceInterface->updateStatus($post);

        return response() -> json($flag);
    }

    public function changeStatusAll(Request $request) {
        $post = $request->input();
        // Khởi tạo import
        $serviceInterface = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterface)) {
            // Sử dụng đường dẫn 
            $serviceInterface = app($serviceInterface);
        }

        $flag = $serviceInterface->updateStatusAll($post);

        return response() -> json($flag);
    }

    public function getMenu(Request $request) {
        $model = $request->input('model');
        $page = $request->input('page', 1);  // Lấy trang hiện tại, mặc định là trang 1 nếu không có tham số
        $keyword = $request->string('search', '');

        $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        
        if (!class_exists($serviceInterfaceNamespace)) {
            return response()->json(['error' => 'Repository not found.'], 404);
        }
        
        $serviceInterface = app($serviceInterfaceNamespace);

        // Kiểm tra sự tồn tại của phương thức pagination
        if (!method_exists($serviceInterface, 'pagination')) {
            return response()->json(['error' => 'Required method not found in repository.'], 500);
        }

        $arguments = $this->paginationAgrument($model, $page, $keyword);

        try {
            $object = $serviceInterface->pagination(...array_values($arguments));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
        return response()->json([
            'data' => $object
        ]);
    }
    
    private function paginationAgrument(string $model = '', int $page = 1, string $keyword = ''): array {
        $model = Str::snake($model);
    
        $column = (strpos($model, '_catalogue') === false) ? $this->paginateSelect($model) : $this->paginateCatalogueSelect($model);
        $join = [
            ["{$model}_language as tb2", "tb2.{$model}_id", "=", "{$model}s.id"]
        ];
        $relation = [];
        $condition = [
            'where' => [
                ['tb2.language_id', '=', $this->language],
            ],
            'keyword' => addslashes($keyword),
        ];  
        $orderBy = ["{$model}s.id", "DESC"];
        $groupBy = (strpos($model, '_catalogue') === false) ? $this->paginateSelect($model) : [];
    
        if (strpos($model, '_catalogue') === false) {
            $join[] = ["{$model}_catalogue_{$model} as tb3", "{$model}s.id", "=", "tb3.{$model}_id"];
            $relation = ["{$model}_catalogues"];
        }
    
        return [
            'column' => $column,
            'condition' => $condition,
            'perpage' => 10,
            'extend' => [
                'path' => "menu/create/getMenu",
                'groupBy' => $groupBy
            ],
            'orderBy' => $orderBy,
            'join' => $join,
            'relations' => $relation,
            'rawQuery' => [],
            "page" => $page
        ];
    }

    public function findModelObject(Request $request) {
        $get = $request->input();
        $repository = $this->loadClassInterface($get['model']);

        $model = Str::snake($get['model']);
        $keyword = addslashes($get['keyword']);

        $column = (strpos($model, '_catalogue') === false) ? $this->paginateSelect($model) : $this->paginateCatalogueSelect($model);

        $object = $repository->findByCondition(
            [
                ['tb2.name', 'LIKE', '%' . $keyword . '%'],
                ['tb2.language_id', '=', $this->language]
            ],
            true,
            [
                [
                    'table' => "{$model}_language as tb2", // Bảng liên kết
                    'on' => ["tb2.{$model}_id", "{$model}s.id"] // Điều kiện join
                ]
            ],
            ["{$model}s.id" => 'ASC'],
            $column,
        );
        
        return response() -> json($object);
    }

    public function getPromotionConditionValue(Request $request) {
        try {
            $get = $request->input();
            switch ($get['value']) {
                case 'staff_take_care_customer':
                    $class = $this->loadClassInterface('User');
                    $objects = $class->all()->toArray();
                    break;
                case 'customer_group':
                    $class = $this->loadClassInterface('Customer');
                    $objects = $class->all()->toArray();
                    break;
                case 'customer_gender':
                    $objects = __('module.gender');
                    break;
                case 'customer_birthday':
                    $objects = __('module.day');
                    break;
            }

            $temp = [];
            if (!is_null($objects) && count($objects) > 0) {
                foreach ($objects as $key => $val) {
                    $temp[] = [
                        'id' => $val['id'],
                        'name' => $val['name']
                    ];
                }
            }
            return response()->json([
                'data' => $temp,
                'error' => false,
                'selectValue' => $get['value']
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'messages' => $e->getMessage(),
                'error' => true,
            ]);
        }   
    }

    private function loadClassInterface(string $model = '', string $folder = 'Repositories',  $interface = 'Repository') {
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . ucfirst($model) . $interface;
        if (!class_exists($serviceInterfaceNamespace)) {
            return response()->json(['error' => 'Repository not found.'], 404);
        }
        
        $serviceInterface = app($serviceInterfaceNamespace);

        return $serviceInterface;
    }

    private function paginateSelect(string $model) {
        return [
            "{$model}s.id", 
            "{$model}s.publish", 
            "{$model}s.image", 
            "{$model}s.order", 
            "tb2.language_id", 
            "tb2.name", 
            "tb2.canonical",
            "{$model}s.image"
        ];
    }

    private function paginateCatalogueSelect(string $model) {
        return [
            "{$model}s.id", 
            "{$model}s.publish", 
            "{$model}s.image", 
            "{$model}s.level", 
            "{$model}s.order", 
            "tb2.name", 
            "tb2.canonical",
            "{$model}s.image"
        ];
    }

}
