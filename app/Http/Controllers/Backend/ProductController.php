<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ProductService as ProductService;
use App\Repositories\ProductRepository as ProductRepository;
use App\Repositories\AttriButeCatalogueRepository as AttriButeCatalogueRepository;

use App\Models\Language;

use App\Http\Request\UpdateProductRequest;
use App\Http\Request\StoreProductRequest;

use App\Classes\Nestedsetbie;

class ProductController extends Controller
{   
    protected $productService;
    protected $productRepository;
    protected $nestedSet;
    protected $language;
    protected $attributeCatalogueRepository;
    
    
    public function __construct(
        ProductService $productService, 
        ProductRepository $productRepository,
        AttriButeCatalogueRepository $attriButeCatalogueRepository
    ) { 
        // Thay vì khai báo ở route để xác 
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this ->initialize();
            // Sau khi xử lý xong nó sẽ truyền $request tới các middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });

        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->attributeCatalogueRepository = $attriButeCatalogueRepository;
        $this->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', 'product.index');
        $products = $this->productService->paginate($request, $this->language);
        $languageSelectId = $this->language;
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Product',
        ];
        $config['seo'] = __('messages.product');
        $template = 'backend.product.product.index';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'products',
            'dropdown',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', 'product.create');
        $attributeCatalogue = $this->attributeCatalogueRepository->getAll($this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'create';
        $template = 'backend.product.product.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'attributeCatalogue'
        ));
    }

    public function store(StoreProductRequest $request) {
        if ($this->productService->create($request, $this->language)) {
            return redirect() -> route('product.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('product.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'product.update');
        $attributeCatalogue = $this->attributeCatalogueRepository->getAll($this->language);
        $config = $this->configData();
        $template = 'backend.product.product.store';
        $config['seo'] = __('messages.product');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        $product = $this->productRepository->getProductById($id, $this->language);
        $album = json_decode($product->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'product',
            'dropdown',
            'album',
            'attributeCatalogue'
        ));
    }

    public function update($id, UpdateProductRequest $request) {
        if ($this->productService->update($id, $request, $this->language)) {
            return redirect() -> route('product.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('product.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'product.destroy');
        $product = $this->productRepository->getProductById($id, $this->language);
        $config['seo'] = __('messages.product');
        $template = 'backend.product.product.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'product',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->productService->delete($id)) {
            return redirect() -> route('product.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('product.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/library/variant.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/nice-select/js/jquery.nice-select.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugin/nice-select/css/nice-select.css',
                'backend/css/plugins/switchery/switchery.css',
            ]
        ];
    }
}
