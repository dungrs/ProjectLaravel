<?php

namespace App\Services;

use App\Services\Interfaces\{Module}CatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\{Module}CatalogueRepository;
use App\Repositories\RouterRepository;

use App\Classes\Nestedsetbie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class {Module}CatalogueService
 * @package App\Services
 */
class {Module}CatalogueService extends BaseService implements {Module}CatalogueServiceInterface
{   
    protected ${module}CatalogueRepository;
    protected $routerRepository;
    protected $nestedSet;
    protected $language;
    protected $controllerName = '{Module}CatalogueController';

    public function __construct({Module}CatalogueRepository ${module}CatalogueRepository, RouterRepository $routerRepository) {
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
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
        ${module}Catalogues = $this->{module}CatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            ['path' => '{module}/index'],
            [
                '{module}_catalogues.lft', 'ASC'
            ],
            [
                ['{module}_catalogue_language as tb2', 'tb2.{module}_catalogue_id', '=', '{module}_catalogues.id']
            ], 
            [], 
        );
        return ${module}Catalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            // Lấy tất cả dữ liệu từ request
            ${module}Catalogue = $this->create{Module}Catalogue($request);
            if (${module}Catalogue->id > 0) {
                $this->updateLanguageFor{Module}Catalogue($request, ${module}Catalogue, $languageId);
                $this->createRouter($request, ${module}Catalogue, $this->controllerName);
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
            ${module}Catalogue = $this->{module}CatalogueRepository->findById($id);
            $flag = $this->update{Module}Catalogue($request, $id);
            if ($flag == true) {
                $this->updateLanguageFor{Module}Catalogue($request, ${module}Catalogue, $languageId);
                $this->updateRouter($request, ${module}Catalogue, $this->controllerName);
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
            $user = $this->{module}CatalogueRepository->delete($id);
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

    public function updateStatus(${module} = []) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = [
                ${module}["field"] => ((${module}['value'] == 1) ? 2 : 1)
            ];
            $this->{module}CatalogueRepository->update(${module}['modelId'], $payload);
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
            $this->{module}CatalogueRepository->updateByWhereIn('id', ${module}['id'], $payload);
            
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
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
            'language_id' => $languageId,
        ]);
    }

    private function create{Module}Catalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->{module}CatalogueRepository->create($payload);
    }

    public function update{Module}Catalogue($request, $id) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->{module}CatalogueRepository->update($id, $payload);
        return $flag;
    }

    private function updateLanguageFor{Module}Catalogue($request, ${module}Catalogue, $languageId) {
        $payload = $this->formatLanguagePayload($request, ${module}Catalogue->id, $languageId);
        // Detach được sử dụng để xóa theo id trong các table many to many
        ${module}Catalogue->languages()->detach($payload['language_id'], ${module}Catalogue->id);
        return $this->{module}CatalogueRepository->createPivot(${module}Catalogue, $payload, 'languages');
    }

    private function formatLanguagePayload($request, $id, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['{module}_catalogue_id'] = $id;
        return $payload;
    }

    private function paginateSelect() {
        return ['{module}_catalogues.id', '{module}_catalogues.publish', '{module}_catalogues.image', '{module}_catalogues.level', '{module}_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['parent_id', 'follow', 'publish', 'image', 'album'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
