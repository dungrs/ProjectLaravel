<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AttributeCatalogueService as AttributeCatalogueService;
use App\Repositories\AttributeCatalogueRepository as AttributeCatalogueRepository;

use App\Models\Language;

use App\Http\Request\UpdateAttributeCatalogueRequest;
use App\Http\Request\StoreAttributeCatalogueRequest;
use App\Http\Request\DeleteAttributeCatalogueRequest;

use App\Classes\Nestedsetbie;

class AttributeCatalogueController extends Controller
{   
    protected $attributeCatalogueService;
    protected $attributeCatalogueRepository;
    protected $nestedSet;
    protected $language;
    
    public function __construct(AttributeCatalogueService $attributeCatalogueService, AttributeCatalogueRepository $attributeCatalogueRepository) {
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
        $this->attributeCatalogueService = $attributeCatalogueService;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this ->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', 'attribute.catalogue.index');
        $attributeCatalogues = $this->attributeCatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'AttributeCatalogue'
        ];
        $config['seo'] = __("messages.attributeCatalogue");
        $template = 'backend.attribute.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attributeCatalogues'
        ));
    }

    public function create() {
        $this->authorize('modules', 'attribute.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.attributeCatalogue');
        $config['method'] = 'create';
        $template = 'backend.attribute.catalogue.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreAttributeCatalogueRequest $request) {
        if ($this->attributeCatalogueService->create($request, $this->language)) {
            return redirect() -> route('attribute.catalogue.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('attribute.catalogue.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'attribute.catalogue.update');
        $config = $this->configData();
        $template = 'backend.attribute.catalogue.store';
        $config['seo'] = __('messages.attributeCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id, $this->language);
        $album = json_decode($attributeCatalogue->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attributeCatalogue',
            'dropdown',
            'album'
        ));
    }

    public function update($id, UpdateAttributeCatalogueRequest $request) {
        if ($this->attributeCatalogueService->update($id, $request, $this->language)) {
            return redirect() -> route('attribute.catalogue.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('attribute.catalogue.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'attribute.catalogue.destroy');
        $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id, $this->language);
        $config['seo'] = __('messages.attributeCatalogue');
        $template = 'backend.attribute.catalogue.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'attributeCatalogue',
            'config'
        ));
    }

    public function destroy($id, DeleteAttributeCatalogueRequest $request) {
        if ($this->attributeCatalogueService->delete($id, $this->language)) {
            return redirect() -> route('attribute.catalogue.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('attribute.catalogue.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function initialize() {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
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
