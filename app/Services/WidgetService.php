<?php

namespace App\Services;
use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\WidgetRepository;
use App\Repositories\PromotionRepository;
use App\Services\PromotionService;
use App\Repositories\ProductCatalogueRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetService extends BaseService implements WidgetServiceInterface
{   
    protected $widgetRepository;
    protected $promotionRepository;
    protected $promotionService;
    protected $productCatalogueRepository;

    public function __construct(
        WidgetRepository $widgetRepository, 
        PromotionRepository $promotionRepository, 
        PromotionService $promotionService, 
        ProductCatalogueRepository $productCatalogueRepository,
        ) {
        $this->widgetRepository = $widgetRepository;
        $this->promotionRepository = $promotionRepository;
        $this->promotionService = $promotionService;
        $this->productCatalogueRepository = $productCatalogueRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $widget = $this->widgetRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'permission/index'], 
            ['id', 'DESC'], 
            []
        );
        return $widget;
    }
    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $this->payload($request, $languageId);
            $this->widgetRepository->create($payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function update($id, $request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $this->payload($request, $languageId); 


            $widget = $this->widgetRepository->update($id, $payload);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function payload($request, $languageId) {
        $payload = $request->only(['name', 'keyword', 'short_code', 'album', 'model']);
        $payload['model_id'] = $request->input('modelItem.id');
        $payload['description'] = json_encode([
            $languageId => $request->input('description'),
        ]);

        return $payload;
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $widget = $this->widgetRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function saveTranslate($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $temp = [];
            $translateId = $request->input('translateId');
            $widgetId = $request->input('widgetId');
            $widget = $this->widgetRepository->findById($widgetId);
            $temp = $widget->description;
            $temp[$translateId] = $request->input('translate_description');
            $payload['description'] = $temp;
            $this->widgetRepository->update($widget->id, $payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function getWidgetItem(string $model = '', array $model_id = [], int $language = 1, array $params = []) {
        $modelName = $model;
        $model = Str::snake($model); // Kết quả: 'post_catalogue'
        $tableName = "{$model}s"; // Tên bảng: 'post_catalogues'
    
        $repositoryInterfaceNamespace = "\\App\\Repositories\\" . ucfirst($modelName) . "Repository";
    
        if (!class_exists($repositoryInterfaceNamespace)) {
            return response()->json(['error' => 'Repository not found.'], 404);
        }
    
        $condition = [
            ["{$model}_language.language_id", '=', $language],
            ["{$model}_language.{$model}_id", 'IN', $model_id]
        ];
    
        $join = [
            [
                'table' => "{$model}_language", // Bảng liên kết
                'on' => ["{$model}_language.{$model}_id", "{$tableName}.id"] // Điều kiện join
            ]
        ];
    
        $repositoryInterface = app($repositoryInterfaceNamespace);
    
        $columns = [
            "{$model}s.id", 
            "{$model}_language.canonical", 
            "{$model}s.image", 
            "{$model}_language.name",
        ];
    
        if (isset($params['object']) && $params['object'] === true) {
            $columns[] = "{$model}s.lft";
            $columns[] = "{$model}s.rgt";
        }

        $widgetItemData = $repositoryInterface->findByCondition(
            $condition,
            true, // Trả về danh sách
            $join,
            ["{$model}s.id" => 'ASC'],
            $columns,
            null // Không phân trang   
        );

        $fields = ['id', 'canonical', 'image', 'name'];
        if (isset($params['object'])) {
            return $widgetItemData;
        }
    
        $widgetItem = convertArray($fields, $widgetItemData);
    
        return $widgetItem;
    }

    public function getWidget(array $conditionKeyword = [], int $languageId = 1) {
        $widget = collect($conditionKeyword)->mapWithKeys(function ($item, $key) use ($languageId) {
            return [$key => $this->findWidgetByKeyword($item['keyword'], $languageId, $item['options'])];
        })->toArray();

        return $widget;
    }
    
    private function paginateSelect() {
        return [
            'id',
            'name',
            'keyword',
            'model',
            'publish',
            'description'
        ];
    }

    // FRONTEND SERVICE 
    public function findWidgetByKeyword(string $keyword = '', int $language, $params = []) {
        $widget = $this->widgetRepository->findByCondition([
            ['keyword', '=', $keyword],
            config('apps.general.defaultPublish')
        ]);
    
        $widgetItems = $this->getWidgetItem($widget->model, $widget->model_id, $language, $params);
        $model = $widget->model;
        $tableName = Str::snake($model); // product_catalogue
        $tableChild = explode('_', $tableName)[0]; // product
        $modelChild = ucfirst($tableChild);
        
        foreach ($widgetItems as $val) {
            $processedProductIds = [];
            // Xử lý khuyến mãi cho sản phẩm chính
            if ($model === 'Product' && isset($params['promotion'])) {
                $this->promotionService->applyPromotionToProduct($val, $tableChild);
            }
    
            // Xử lý danh mục con nếu có tham số `children`
            if (isset($params['children']) && $params['children'] === true) {
                $parentAndChildID = $this->widgetRepository->recursveCategory($val->id, $tableChild);
                $childObjectCatalogue = $this->getWidgetItem($widget->model, $parentAndChildID, $language, $params);
    
                foreach ($childObjectCatalogue as $childVal) {
                    // Lấy các sản phẩm con và xử lý
                    $childItems = $this->getItemsByParent($modelChild, $tableName, $tableChild, $language, $childVal->id)
                        ->filter(function ($item) use (&$processedProductIds) {
                            if (!in_array($item->id, $processedProductIds)) {
                                $processedProductIds[] = $item->id;
                                return true;
                            }
                            return false;
                        });
    
                    // Xử lý khuyến mãi cho sản phẩm con
                    if (isset($params['promotion']) && !empty($childVal->products) && $childVal->products->isNotEmpty()) {
                        $childItems = $this->promotionService->applyPromotionToProductCollection($childItems, $childVal->products, $tableChild);
                    }
    
                    $childVal->productLists = $childItems;
                }

                $val->product_count = count($processedProductIds);
                $val->children = $childObjectCatalogue;
            }
        }
    
        $widget->object = $widgetItems;
        return $widget;
    }
}