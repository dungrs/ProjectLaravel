<?php

namespace App\Services;
use App\Services\Interfaces\AttributeServiceInterface;
use App\Repositories\RouterRepository;
use App\Services\BaseService;
use App\Repositories\AttributeRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{   
    protected $attributeRepository;
    protected $routerRepository;

    public function __construct(AttributeRepository $attributeRepository, RouterRepository $routerRepository) {
        $this->attributeRepository = $attributeRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'AttributeController';
    }

    public function paginate($request, $languageId) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId]
        ];
        $condition['attribute_catalogue_id'] = $request->integer('attribute_catalogue_id');
        $perpage = $request->integer('perpage');
        $attributes = $this->attributeRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            [
                'path' => 'attribute/index', 
                'groupBy' => $this->paginateSelect()
            ],
            ['attributes.id', 'DESC'],
            [
                ['attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id'],
                ['attribute_catalogue_attribute as tb3', 'attributes.id', '=', 'tb3.attribute_id']
            ], 
            ['attribute_catalogues'],
            $this->whereRaw($request, $languageId)
        );
        return $attributes;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            $attribute = $this->createAttribute($request);
            if ($attribute->id > 0) {
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->uploadCatalogueForAttribute($attribute, $request);
                $this->createRouter($request, $attribute, $this->controllerName, $languageId);
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
            $attribute = $this->attributeRepository->findById($id);
            if ($this->uploadAttribute($id, $request)) {
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->uploadCatalogueForAttribute($attribute, $request);
                $this->updateRouter($request, $attribute, $this->controllerName, $languageId);
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
            $this->attributeRepository->delete($id); // Soft Delete
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }
    private function createAttribute($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->attributeRepository->create($payload);
    }

    private function uploadAttribute($id, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->attributeRepository->update($id, $payload);
    }

    private function updateLanguageForAttribute($attribute, $request, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $attribute->id, $languageId
    );
        // Xóa các bản ghi có payload['language_id'] và $attribute->id trong bảng pivot
        $attribute->languages()->detach($payload['language_id'], $attribute->id);
        return $this->attributeRepository->createPivot($attribute, $payload, 'languages');
    }

    private function formatLanguagePayload($payload, $attributeId, $languageId) {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_id'] = $attributeId;
        return $payload;
    }

    private function uploadCatalogueForAttribute($attribute, $request) {
        // Đồng bộ hóa mối quan hệ giữa các dữ liệu
        // Xóa liên kết cũ, thêm liên kết mới và cập nhật liên kết
        $attribute->attribute_catalogues()->sync($this->catalogue($request));
    }
        
    private function catalogue($request) {
        // Lấy mảng danh mục từ input của request và kết hợp với danh mục attribute_catalogue_id
        if ($request->input('catalogue') != null) {
            // Trả về mảng kết quả với các giá trị duy nhất, loại bỏ các phần tử trùng lặp
            return array_unique(
                array_merge(
                    $request->input('catalogue'),  // Lấy các giá trị từ input 'catalogue'
                    [$request->attribute_catalogue_id]  // Thêm giá trị 'attribute_catalogue_id' vào mảng
                )
            );
        }

        return [$request->attribute_catalogue_id];
    }

    private function whereRaw($request, $languageId) {
        $rawCondition = [];
        if ($request->integer('attribute_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    "tb3.attribute_catalogue_id IN (
                        SELECT attribute_catalogues.id
                        FROM attribute_catalogues
                        JOIN attribute_catalogue_language 
                            ON attribute_catalogues.id = attribute_catalogue_language.attribute_catalogue_id
                        WHERE attribute_catalogues.lft >= (
                                SELECT pc.lft 
                                FROM attribute_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND attribute_catalogues.rgt <= (
                                SELECT pc.rgt 
                                FROM attribute_catalogues AS pc 
                                WHERE pc.id = ?
                            )
                        AND attribute_catalogue_language.language_id = ?
                    )
                    ",
                    [$request->integer('attribute_catalogue_id'), $request->integer('attribute_catalogue_id'), $languageId]
                ]
            ];
        }

        return $rawCondition;
    }

    private function paginateSelect() {
        return ['attributes.id', 'attributes.publish', 'attributes.image', 'attributes.order', 'tb2.language_id', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['follow', 'publish', 'image', 'album', 'attribute_catalogue_id'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
