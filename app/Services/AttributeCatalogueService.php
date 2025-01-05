<?php

namespace App\Services;

use App\Services\Interfaces\AttributeCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\AttributeCatalogueRepository;
use App\Repositories\RouterRepository;

use App\Classes\Nestedsetbie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class AttributeCatalogueService extends BaseService implements AttributeCatalogueServiceInterface
{   
    protected $attributeCatalogueRepository;
    protected $routerRepository;
    protected $nestedSet;
    protected $language;
    protected $controllerName = 'AttributeCatalogueController';

    public function __construct(AttributeCatalogueRepository $attributeCatalogueRepository, RouterRepository $routerRepository) {
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->routerRepository = $routerRepository;
        
    }

    public function paginate($request, $languageId) {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request -> input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $attributeCatalogues = $this->attributeCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            ['path' => 'attribute/catalogue/index'],
            [
                'attribute_catalogues.lft', 'ASC'
            ],
            [
                ['attribute_catalogue_language as tb2', 'tb2.attribute_catalogue_id', '=', 'attribute_catalogues.id']
            ], 
            [], 
        );
        return $attributeCatalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            // Lấy tất cả dữ liệu từ request
            $attributeCatalogue = $this->createAttributeCatalogue($request);
            if ($attributeCatalogue->id > 0) {
                $this->updateLanguageForAttributeCatalogue($request, $attributeCatalogue, $languageId);
                $this->createRouter($request, $attributeCatalogue, $this->controllerName, $languageId);
                $this->initialize($languageId);
                $this->nestedSet();
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

    public function update($id, $request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {   
            $attributeCatalogue = $this->attributeCatalogueRepository->findById($id);
            $flag = $this->updateAttributeCatalogue($request, $id);
            if ($flag == true) {
                $this->updateLanguageForAttributeCatalogue($request, $attributeCatalogue, $languageId);
                $this->updateRouter($request, $attributeCatalogue, $this->controllerName, $languageId);
                $this->initialize($languageId);
                $this->nestedSet();
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

    public function delete($id, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $user = $this->attributeCatalogueRepository->delete($id);
            $this->initialize($languageId);
            $this->nestedSet->Get();
            $this->nestedSet->Recursive(0, $this->nestedSet->Set());
            $this->nestedSet->Action();
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }


    private function initialize($languageId) {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
            'language_id' => $languageId,
        ]);
    }

    private function createAttributeCatalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->attributeCatalogueRepository->create($payload);
    }

    public function updateAttributeCatalogue($request, $id) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->attributeCatalogueRepository->update($id, $payload);
        return $flag;
    }

    private function updateLanguageForAttributeCatalogue($request, $attributeCatalogue, $languageId) {
        $payload = $this->formatLanguagePayload($request, $attributeCatalogue->id, $languageId);
        // Detach được sử dụng để xóa theo id trong các table many to many
        $attributeCatalogue->languages()->detach($payload['language_id'], $attributeCatalogue->id);
        return $this->attributeCatalogueRepository->createPivot($attributeCatalogue, $payload, 'languages');
    }

    private function formatLanguagePayload($request, $id, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_catalogue_id'] = $id;
        return $payload;
    }

    private function paginateSelect() {
        return ['attribute_catalogues.id', 'attribute_catalogues.publish', 'attribute_catalogues.image', 'attribute_catalogues.level', 'attribute_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['parent_id', 'follow', 'publish', 'image', 'album'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
