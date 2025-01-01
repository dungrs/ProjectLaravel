<?php

namespace App\Services;
use App\Services\Interfaces\BaseServiceInterface;
use App\Repositories\RouterRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{   

    protected $routerRepository;
    protected $controllerName;
    public function __construct(RouterRepository $routerRepository) {
        $this->routerRepository = $routerRepository;
    }

    public function formatAlbum($request) {
        return ($request->input('album') && !empty($request->input('album'))) ? json_encode($request->input('album')): '';
    }

    public function formatJson($request, $inputName) {
        return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)): '';
    }

    public function nestedSet() {
        $this->nestedSet->Get();
        $this->nestedSet->Recursive(0, $this->nestedSet->Set());
        $this->nestedSet->Action();
    }

    public function formatRouterPayload($request, $model, $controllerName, $languageId) {
        return  [
            'canonical' => $request->input('canonical'),
            'module_id' => $model->id,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Frontend\\'. $controllerName .'',
        ];
    }

    public function createRouter($request, $model, $controllerName, $languageId) {
        $payloadRouter = $this->formatRouterPayload($request, $model, $controllerName, $languageId);
        $this->routerRepository->create($payloadRouter);
    }

    public function updateRouter($request, $model, $controllerName, $languageId) {
        $payload = $this->formatRouterPayload($request, $model, $controllerName, $languageId);
        $condition =  [
            ['module_id', '=', $model->id],
            ['controllers', '=', 'App\Http\Controllers\Frontend\\'.$controllerName .'']
        ];
        $router = $this->routerRepository->findByCondition($condition);
        $res = $this->routerRepository->update($router->id, $payload); 
        return $res;
    }

    public function updateStatus($model = []) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                $model["field"] => (($model['value'] == 1) ? 2 : 1)
            ];
            $modelName = lcfirst($model['model']);
            $repository = $this->{$modelName . 'Repository'};
            $repository->update($model['modelId'], $payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (\Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function updateStatusAll($model = []) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                $model["field"] => $model["value"]
            ];
            $modelName = lcfirst($model['model']);
            $repository = $this->{$modelName . 'Repository'};
            $repository->updateByWhereIn($model['modelId'], $payload);
            
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (\Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    protected function loadClass(string $model = '', string $folder = 'Repositories',  $interface = 'Repository') {
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . ucfirst($model) . $interface;
        if (!class_exists($serviceInterfaceNamespace)) {
            return response()->json(['error' => 'Repository not found.'], 404);
        }
        
        $serviceInterface = app($serviceInterfaceNamespace);

        return $serviceInterface;
    }

    protected function getItemsByParent($model, $parentTable, $childTable, $language, $parentIds) {
        $items = loadClass($model)->findByCondition(
            [
                ["{$childTable}s.publish", '=', 2], // Lọc sản phẩm đã xuất bản
                ["{$parentTable}_{$childTable}.{$parentTable}_id", '=', $parentIds], // Lọc theo danh mục sản phẩm
                ["{$childTable}_language.language_id", '=', $language], // Lọc theo ngôn ngữ
            ],
            true,
            [
                [
                    'table' => "{$childTable}_language",
                    'on' => ["{$childTable}_language.{$childTable}_id", "{$childTable}s.id"]
                ],
                [
                    'table' => "{$parentTable}_{$childTable}",
                    'on' => ["{$parentTable}_{$childTable}.{$childTable}_id", "{$childTable}s.id"] // Sửa lại ON để khớp SQL
                ]
            ],
            ["{$childTable}s.id" => 'ASC'], // Sắp xếp theo ID tăng dần
            [
                "{$childTable}s.id", 
                "{$childTable}_language.canonical", 
                "{$childTable}s.image", 
                "{$childTable}_language.name"
            ]
        );
    
        return $items;
    }
    
}
