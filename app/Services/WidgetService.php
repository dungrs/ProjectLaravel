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

    public function getWidgetItem(string $model = '', array $model_id = [], int $language = 1) {
        $modelName = $model;
        $model = Str::snake($model); // Kết quả: 'post_catalogue'
        $tableName = "{$model}s"; // Tên bảng: 'post_catalogues'
    
        // Lấy tên repository dựa trên quy ước
        $repositoryInterfaceNamespace = "\App\Repositories\\" . ucfirst($modelName) . "Repository";
    
        // Kiểm tra repository có tồn tại không
        if (!class_exists($repositoryInterfaceNamespace)) {
            return response()->json(['error' => 'Repository not found.'], 404);
        }
    
        $repositoryInterface = app($repositoryInterfaceNamespace);
    
        $widgetItemData = $repositoryInterface->findByCondition(
            [
                ['language_id', '=', $language],
                ["{$model}_language.{$model}_id", 'IN' , $model_id]
            ],
            true, // Trả về danh sách
            [
                "{$model}_language" => ["{$model}_language.{$model}_id", "{$tableName}.id"],
            ],
            ["{$model}s.id" => 'ASC'],
            ["{$model}s.id", 'canonical', 'image', 'name']
        );

        $feild = ['id', 'canonical', 'image', 'name'];

        $widgetItem = convertArray($feild, $widgetItemData);
    
        return $widgetItem;
    }
    private function paginateSelect() {
        return [
            'id',
            'name',
            'keyword',
            'model',
            'publish'
        ];
    }
}
