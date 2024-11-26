<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AttributeService as AttributeService;
use App\Repositories\AttributeRepository as AttributeRepository;

use App\Models\Language;

use App\Http\Request\UpdateAttributeRequest;
use App\Http\Request\StoreAttributeRequest;

use App\Classes\Nestedsetbie;

class AttributeController extends Controller
{   
    protected $attributeService;
    protected $attributeRepository;
    protected $nestedSet;
    protected $language;
    
    
    public function __construct(
        AttributeService $attributeService, 
        AttributeRepository $attributeRepository,
    ) { 
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

        $this->attributeService = $attributeService;
        $this->attributeRepository = $attributeRepository;
        $this->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', 'attribute.index');
        $attributes = $this->attributeService->paginate($request, $this->language);
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
            'model' => 'Attribute',
        ];
        $config['seo'] = __('messages.attribute');
        $template = 'backend.attribute.attribute.index';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attributes',
            'dropdown',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', 'attribute.create');
        $config = $this->configData();
        $config['seo'] = __('messages.attribute');
        $config['method'] = 'create';
        $template = 'backend.attribute.attribute.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreAttributeRequest $request) {
        if ($this->attributeService->create($request, $this->language)) {
            return redirect() -> route('attribute.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('attribute.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'attribute.update');
        $config = $this->configData();
        $template = 'backend.attribute.attribute.store';
        $config['seo'] = __('messages.attribute');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
        $album = json_decode($attribute->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attribute',
            'dropdown',
            'album'
        ));
    }

    public function update($id, UpdateAttributeRequest $request) {
        if ($this->attributeService->update($id, $request, $this->language)) {
            return redirect() -> route('attribute.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('attribute.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'attribute.destroy');
        $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
        $config['seo'] = __('messages.attribute');
        $template = 'backend.attribute.attribute.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'attribute',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->attributeService->delete($id)) {
            return redirect() -> route('attribute.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('attribute.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
