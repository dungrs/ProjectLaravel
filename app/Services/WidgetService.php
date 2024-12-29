<?php

namespace App\Services;
use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\WidgetRepository;
use App\Repositories\PromotionRepository;
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
    protected $productCatalogueRepository;

    public function __construct(WidgetRepository $widgetRepository, PromotionRepository $promotionRepository, ProductCatalogueRepository $productCatalogueRepository) {
        $this->widgetRepository = $widgetRepository;
        $this->promotionRepository = $promotionRepository;
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
    
        if (isset($params['children'])) {
            $columns[] = "{$model}s.lft";
            $columns[] = "{$model}s.rgt";
            $columns[] = DB::raw(
                "(
                    SELECT COUNT(*) 
                    FROM `products` 
                    WHERE `products`.`product_catalogue_id` = `{$tableName}`.`id` 
                      AND `products`.`deleted_at` IS NULL
                ) AS product_count"
            );
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
        if (isset($params['children'])) {
            return $widgetItemData;
        }
    
        $widgetItem = convertArray($fields, $widgetItemData);
    
        return $widgetItem;
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
        $widget = $this->widgetRepository->findByCondition(
            [
                ['keyword', '=', $keyword],
                config('apps.general.defaultPublish')
            ],
        );

        $widgetItems = $this->getWidgetItem($widget->model, $widget->model_id, $language, $params = ['children' => true]);
        if (isset($params['children'])) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            
            foreach ($widgetItems as $key => $val) {
                if (!empty($val->products) && $val->products->isNotEmpty()) {
                    $productId = $val->products->pluck('id');
            
                    $promotion = $this->promotionRepository->findByCondition(
                        [
                            ['promotions.publish', '=', 2],
                            ['products.publish', '=', 2],
                            ['products.id', 'IN', $productId],
                        ],
                        true, 
                        [
                            [
                                'table' => "promotion_product_variant as ppv",
                                'on' => ["ppv.promotion_id", "promotions.id"]
                            ],
                            [
                                'table' => "products",
                                'on' => ["products.id", "ppv.product_id"]
                            ],
                            [
                                'table' => "product_variants",
                                'on' => ["product_variants.uuid", "ppv.variant_uuid"]
                            ]
                        ],
                        ["products.id" => 'ASC'], // Sắp xếp theo products.id
                        [
                            DB::raw("
                                MAX(
                                    LEAST(
                                        CASE
                                            WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                                            WHEN promotions.discountType = 'percent' THEN (product_variants.price * promotions.discountValue / 100)
                                            ELSE 0
                                        END,
                                        CASE
                                            WHEN promotions.maxDiscountValue > 0 THEN promotions.maxDiscountValue
                                            ELSE 1e9 -- Một giá trị lớn để loại bỏ maxDiscountValue nếu nó bằng 0
                                        END
                                    )
                                ) AS finalDiscount
                            "),
                            "products.id AS product_id",
                            "product_variants.price AS product_price",
                            "promotions.discountType",
                            "promotions.discountValue",
                            "promotions.maxDiscountValue",
                        ],
                        null,
                        [], // Không sử dụng eager loading trong trường hợp này
                        [
                            'products.id', 
                            'product_variants.price', // Thêm vào `GROUP BY` trường `product_variants.price`
                            'promotions.discountType', 
                            'promotions.discountValue', 
                            'promotions.maxDiscountValue'
                        ] // Áp dụng groupBy cho các trường cần thiết
                    );
            
                    // Lọc các promotions theo product_id và chọn promotion có finalDiscount lớn nhất
                    $bestPromotions = collect($promotion)
                        ->groupBy('product_id') // Nhóm theo product_id
                        ->map(function ($group) {
                            // Chọn bản ghi có finalDiscount lớn nhất
                            return $group->sortByDesc('finalDiscount')->first();
                        })
                        ->values();
            
                        
                        $val->products = $bestPromotions;
                } 
                if (!empty($params['children']) && $params['children'] === true) {
                    $childProductCatalogue = $this->productCatalogueRepository->findByCondition(
                        [
                            ["product_catalogues.lft", '>', $val->lft],
                            ["product_catalogues.rgt", '<', $val->rgt]
                        ],
                        true
                    );
                } else {
                    $childProductCatalogue = null;
                }
                $val->children = $childProductCatalogue;
            }
        }
        
        return $widgetItems;
    }
}