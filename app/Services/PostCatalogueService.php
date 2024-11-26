<?php

namespace App\Services;
use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\PostCatalogueRepository;
use App\Repositories\RouterRepository;
use App\Classes\Nestedsetbie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class PostCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{   
    protected $postCatalogueRepository;
    protected $routerRepository;
    protected $nestedSet;
    protected $language;
    protected $controllerName = 'PostCatalogueController';

    public function __construct(PostCatalogueRepository $postCatalogueRepository, RouterRepository $routerRepository) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routerRepository = $routerRepository;
        
    }

    public function paginate($request, $languageId) {
        $perpage = 10;
        $condition = [
            'keyword' => addslashes($request -> input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $postCatalogues = $this->postCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            ['path' => 'post/catalogue/index'],
            [
                'post_catalogues.lft', 'ASC'
            ],
            [
                ['post_catalogue_language as tb2', 'tb2.post_catalogue_id', '=', 'post_catalogues.id']
            ], 
            [], 
        );
        return $postCatalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            // Lấy tất cả dữ liệu từ request
            $postCatalogue = $this->createPostCatalogue($request);
            if ($postCatalogue->id > 0) {
                $this->updateLanguageForPostCatalogue($request, $postCatalogue, $languageId);
                $this->createRouter($request, $postCatalogue, $this->controllerName, $languageId);
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
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            $flag = $this->updatePostCatalogue($request, $id);
            if ($flag == true) {
                $this->updateLanguageForPostCatalogue($request, $postCatalogue, $languageId);
                $this->updateRouter($request, $postCatalogue, $this->controllerName, $languageId);
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
            $this->postCatalogueRepository->delete($id);
            $this->routerRepository->deleteByCondition([
                ['module_id', '=', $id],
                ['controllers' , '=', 'App\Http\Controllers\Frontend\PostCatalogueController']
            ]);
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
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $languageId,
        ]);
    }

    private function createPostCatalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->postCatalogueRepository->create($payload);
    }

    public function updatePostCatalogue($request, $id) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->postCatalogueRepository->update($id, $payload);
        return $flag;
    }

    private function updateLanguageForPostCatalogue($request, $postCatalogue, $languageId) {
        $payload = $this->formatLanguagePayload($request, $postCatalogue->id, $languageId);
        // Detach được sử dụng để xóa theo id trong các table many to many
        $postCatalogue->languages()->detach($payload['language_id'], $postCatalogue->id);
        return $this->postCatalogueRepository->createPivot($postCatalogue, $payload, 'languages');
    }

    private function formatLanguagePayload($request, $id, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_catalogue_id'] = $id;
        return $payload;
    }

    private function paginateSelect() {
        return ['post_catalogues.id', 'post_catalogues.publish', 'post_catalogues.image', 'post_catalogues.level', 'post_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['parent_id', 'follow', 'publish', 'image', 'album'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
