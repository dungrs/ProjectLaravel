<?php

namespace App\Services;
use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\WidgetRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetService extends BaseService implements WidgetServiceInterface
{   
    protected $widgetRepository;

    public function __construct(WidgetRepository $widgetRepository) {
        $this->widgetRepository = $widgetRepository;
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
    
        // Lấy tên repository dựa trên quy ước
        $repositoryInterfaceNamespace = "\\App\\Repositories\\" . ucfirst($modelName) . "Repository";
    
        // Kiểm tra repository có tồn tại không
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
    
        // Kiểm tra tham số children và bổ sung join nếu cần
        if (!empty($params['children'])) {
            $tableChild = lcfirst(str_replace('Catalogue', '', $modelName));
    
            $join[] = [
                'type' => 'left',
                'table' => "{$tableChild}s", // Bảng liên kết
                'on' => ["{$tableChild}s.{$tableChild}_catalogue_id", "{$tableName}.id"] // Điều kiện join
            ];
    
            $join[] = [
                'type' => 'left',
                'table' => "{$tableChild}_language", // Bảng liên kết
                'on' => ["{$tableChild}_language.{$tableChild}_id", "{$tableChild}s.id"] // Điều kiện join
            ];
        }
    
        $repositoryInterface = app($repositoryInterfaceNamespace);
    
        // Lấy dữ liệu widget item
        $columns = [
            "{$model}s.id", 
            "{$model}_language.canonical", 
            "{$model}s.image", 
            "{$model}_language.name",
        ];
    
        if (!empty($params['children'])) {
            $columns[] = "{$tableChild}_language.name as child_name"; // Nếu có children, lấy thêm name của child
            $columns[] = DB::raw('COUNT(' . "{$tableChild}s.id" . ') as child_count'); // Đếm số lượng child
        }
    
        // Cập nhật phần groupBy
        $groupBy = [
            "{$model}s.id",
            "{$model}_language.canonical",
            "{$model}s.image",
            "{$model}_language.name",
        ];
    
        if (!empty($params['children'])) {
            $groupBy[] = "{$tableChild}_language.name";
        }
    
        // Sử dụng hàm findByCondition với điều kiện, join, groupBy và các cột cần lấy
        $widgetItemData = $repositoryInterface->findByCondition(
            $condition,
            true, // Trả về danh sách
            $join,
            ["{$model}s.id" => 'ASC'],
            $columns,
            null, // Không phân trang
            $groupBy
        );
    
        // Chuyển đổi dữ liệu thành mảng theo trường cần lấy
        $fields = ['id', 'canonical', 'image', 'name'];
    
        if (!empty($params['children'])) {
            $fields[] = 'child_name';
            $fields[] = 'child_count'; // Thêm trường child_count
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

        $widgetItems = $this->getWidgetItem($widget->model, $widget->model_id, $language, $params);
        return $widgetItems;
    }
}
