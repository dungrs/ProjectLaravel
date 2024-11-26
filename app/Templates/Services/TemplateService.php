<?php

namespace App\Services;
use App\Services\Interfaces\{Module}ServiceInterface;
use App\Repositories\RouterRepository;
use App\Services\BaseService;
use App\Repositories\{Module}Repository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class {Module}Service
 * @package App\Services
 */
class {Module}Service extends BaseService implements {Module}ServiceInterface
{   
    protected ${module}Repository;
    protected $routerRepository;

    public function __construct({Module}Repository ${module}Repository, RouterRepository $routerRepository) {
        $this->{module}Repository = ${module}Repository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = '{Module}Controller';
    }

    public function paginate($request, $languageId) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId]
        ];
        $condition['{module}_catalogue_id'] = $request->integer('{module}_catalogue_id');
        $perpage = $request->integer('perpage');
        ${module}s = $this->{module}Repository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            [
                'path' => '{module}/catalogue/index', 
                'groupBy' => $this->paginateSelect()
            ],
            ['{module}s.id', 'DESC'],
            [
                ['{module}_language as tb2', 'tb2.{module}_id', '=', '{module}s.id'],
                ['{module}_catalogue_{module} as tb3', '{module}s.id', '=', 'tb3.{module}_id']
            ], 
            ['{module}_catalogues'],
            $this->whereRaw($request, $languageId)
        );
        return ${module}s;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            ${module} = $this->create{Module}($request);
            if (${module}->id > 0) {
                $this->updateLanguageFor{Module}(${module}, $request, $languageId);
                $this->uploadCatalogueFor{Module}(${module}, $request);
                $this->createRouter($request, ${module}, $this->controllerName);
            }
            DB::commit();
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
            ${module} = $this->{module}Repository->findById($id);
            if ($this->upload{Module}($id, $request)) {
                $this->updateLanguageFor{Module}(${module}, $request, $languageId);
                $this->uploadCatalogueFor{Module}(${module}, $request);
                $this->updateRouter($request, ${module}, $this->controllerName);
            }

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $this->{module}Repository->delete($id); // Soft Delete
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function updateStatus(${module} = []) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                ${module}["field"] => ((${module}['value'] == 1) ? 2 : 1)
            ];
            $this->{module}Repository->update(${module}['modelId'], $payload);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function updateStatusAll(${module}) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                ${module}["field"] => ${module}["value"]
            ];
            $this->{module}Repository->updateByWhereIn('id', ${module}['id'], $payload);
            
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function create{Module}($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->{module}Repository->create($payload);
    }

    private function upload{Module}($id, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->{module}Repository->update($id, $payload);
    }

    private function updateLanguageFor{Module}(${module}, $request, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, ${module}->id, $languageId
    );
        // Xóa các bản ghi có payload['language_id'] và ${module}->id trong bảng pivot
        ${module}->languages()->detach($payload['language_id'], ${module}->id);
        return $this->{module}Repository->createPivot(${module}, $payload, 'languages');
    }

    private function formatLanguagePayload($payload, ${module}Id, $languageId) {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['{module}_id'] = ${module}Id;
        return $payload;
    }

    private function uploadCatalogueFor{Module}(${module}, $request) {
        // Đồng bộ hóa mối quan hệ giữa các dữ liệu
        // Xóa liên kết cũ, thêm liên kết mới và cập nhật liên kết
        ${module}->{module}_catalogues()->sync($this->catalogue($request));
    }
        
    private function catalogue($request) {
        // Lấy mảng danh mục từ input của request và kết hợp với danh mục {module}_catalogue_id
        if ($request->input('catalogue') != null) {
            // Trả về mảng kết quả với các giá trị duy nhất, loại bỏ các phần tử trùng lặp
            return array_unique(
                array_merge(
                    $request->input('catalogue'),  // Lấy các giá trị từ input 'catalogue'
                    [$request->{module}_catalogue_id]  // Thêm giá trị '{module}_catalogue_id' vào mảng
                )
            );
        }

        return [$request->{module}_catalogue_id];
    }

    private function whereRaw($request, $languageId) {
        $rawCondition = [];
        if ($request->integer('{module}_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    "tb3.{module}_catalogue_id IN (
                        SELECT {module}_catalogues.id
                        FROM {module}_catalogues
                        JOIN {module}_catalogue_language 
                            ON {module}_catalogues.id = {module}_catalogue_language.{module}_catalogue_id
                        WHERE {module}_catalogues.lft >= (
                                SELECT pc.lft 
                                FROM {module}_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND {module}_catalogues.rgt <= (
                                SELECT pc.rgt 
                                FROM {module}_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND {module}_catalogue_language.language_id = ?
                    )
                    ",
                    [$request->integer('{module}_catalogue_id'), $request->integer('{module}_catalogue_id'), $languageId]
                ]
            ];
        }

        return $rawCondition;
    }

    private function paginateSelect() {
        return ['{module}s.id', '{module}s.publish', '{module}s.image', '{module}s.order', 'tb2.language_id', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['follow', 'publish', 'image', 'album', '{module}_catalogue_id'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
