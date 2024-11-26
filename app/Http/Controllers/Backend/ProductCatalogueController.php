<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ProductCatalogueService as ProductCatalogueService;
use App\Repositories\ProductCatalogueRepository as ProductCatalogueRepository;

use App\Models\Language;

use App\Http\Request\UpdateProductCatalogueRequest;
use App\Http\Request\StoreProductCatalogueRequest;
use App\Http\Request\DeleteProductCatalogueRequest;

use App\Classes\Nestedsetbie;

class ProductCatalogueController extends Controller
{   
    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $nestedSet;
    protected $language;
    
    public function __construct(ProductCatalogueService $productCatalogueService, ProductCatalogueRepository $productCatalogueRepository) {
        // Thay vì khai báo ở route để xác 
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this ->initialize();
            // Sau khi xử lý xong nó sẽ truyền $request tới cấc middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
        $this->productCatalogueService = $productCatalogueService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this ->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', 'product.catalogue.index');
        $productCatalogues = $this->productCatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'ProductCatalogue'
        ];
        $config['seo'] = __("messages.productCatalogue");
        $template = 'backend.product.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'productCatalogues'
        ));
    }

    public function create() {
        $this->authorize('modules', 'product.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.productCatalogue');
        $config['method'] = 'create';
        $template = 'backend.product.catalogue.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreProductCatalogueRequest $request) {
        if ($this->productCatalogueService->create($request, $this->language)) {
            return redirect() -> route('product.catalogue.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('product.catalogue.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'product.catalogue.update');
        $config = $this->configData();
        $template = 'backend.product.catalogue.store';
        $config['seo'] = __('messages.productCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $album = json_decode($productCatalogue->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'productCatalogue',
            'dropdown',
            'album'
        ));
    }

    public function update($id, UpdateProductCatalogueRequest $request) {
        if ($this->productCatalogueService->update($id, $request, $this->language)) {
            return redirect() -> route('product.catalogue.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('product.catalogue.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'product.catalogue.destroy');
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $config['seo'] = __('messages.productCatalogue');
        $template = 'backend.product.catalogue.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'productCatalogue',
            'config'
        ));
    }

    public function destroy($id, DeleteProductCatalogueRequest $request) {
        if ($this->productCatalogueService->delete($id, $this->language)) {
            return redirect() -> route('product.catalogue.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('product.catalogue.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function initialize() {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

    private function configData() {
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];
    }
}
