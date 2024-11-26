<?php

namespace App\Services;

use App\Services\Interfaces\ProductCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\ProductCatalogueRepository;
use App\Repositories\RouterRepository;

use App\Classes\Nestedsetbie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class ProductCatalogueService
 * @package App\Services
 */
class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{   
    protected $productCatalogueRepository;
    protected $routerRepository;
    protected $nestedSet;
    protected $language;
    protected $controllerName = 'ProductCatalogueController';

    public function __construct(ProductCatalogueRepository $productCatalogueRepository, RouterRepository $routerRepository) {
        $this->productCatalogueRepository = $productCatalogueRepository;
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
        $productCatalogues = $this->productCatalogueRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage,
            ['path' => 'product/catalogue/index'],
            [
                'product_catalogues.lft', 'ASC'
            ],
            [
                ['product_catalogue_language as tb2', 'tb2.product_catalogue_id', '=', 'product_catalogues.id']
            ], 
            [], 
        );
        return $productCatalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            // Lấy tất cả dữ liệu từ request
            $productCatalogue = $this->createProductCatalogue($request);
            if ($productCatalogue->id > 0) {
                $this->updateLanguageForProductCatalogue($request, $productCatalogue, $languageId);
                $this->createRouter($request, $productCatalogue, $this->controllerName, $languageId);
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
            $productCatalogue = $this->productCatalogueRepository->findById($id);
            $flag = $this->updateProductCatalogue($request, $id);
            if ($flag == true) {
                $this->updateLanguageForProductCatalogue($request, $productCatalogue, $languageId);
                $this->updateRouter($request, $productCatalogue, $this->controllerName, $languageId);
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
            $this->productCatalogueRepository->delete($id);
            $this->routerRepository->deleteByCondition([
                ['module_id', '=', $id],
                ['controllers' , '=', 'App\Http\Controllers\Frontend\ProductCatalogueController']
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
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $languageId,
        ]);
    }

    private function createProductCatalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        return $this->productCatalogueRepository->create($payload);
    }

    public function updateProductCatalogue($request, $id) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->productCatalogueRepository->update($id, $payload);
        return $flag;
    }

    private function updateLanguageForProductCatalogue($request, $productCatalogue, $languageId) {
        $payload = $this->formatLanguagePayload($request, $productCatalogue->id, $languageId);
        // Detach được sử dụng để xóa theo id trong các table many to many
        $productCatalogue->languages()->detach($payload['language_id'], $productCatalogue->id);
        return $this->productCatalogueRepository->createPivot($productCatalogue, $payload, 'languages');
    }

    private function formatLanguagePayload($request, $id, $languageId) {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_catalogue_id'] = $id;
        return $payload;
    }

    private function paginateSelect() {
        return ['product_catalogues.id', 'product_catalogues.publish', 'product_catalogues.image', 'product_catalogues.level', 'product_catalogues.order', 'tb2.name', 'tb2.canonical'];
    }

    private function payload() {
        return ['parent_id', 'follow', 'publish', 'image', 'album'];
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
