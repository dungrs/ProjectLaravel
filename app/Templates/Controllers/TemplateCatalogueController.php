<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{Module}CatalogueService as {Module}CatalogueService;
use App\Repositories\{Module}CatalogueRepository as {Module}CatalogueRepository;

use App\Models\Language;

use App\Http\Request\Update{Module}CatalogueRequest;
use App\Http\Request\Store{Module}CatalogueRequest;
use App\Http\Request\Delete{Module}CatalogueRequest;

use App\Classes\Nestedsetbie;

class {Module}CatalogueController extends Controller
{   
    protected ${module}CatalogueService;
    protected ${module}CatalogueRepository;
    protected $nestedSet;
    protected $language;
    
    public function __construct({Module}CatalogueService ${module}CatalogueService, {Module}CatalogueRepository ${module}CatalogueRepository) {
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
        $this->{module}CatalogueService = ${module}CatalogueService;
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
        $this ->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', '{module}.catalogue.index');
        ${module}Catalogues = $this->{module}CatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => '{Module}Catalogue'
        ];
        $config['seo'] = __("messages.{module}Catalogue");
        $template = 'backend.{module}.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}Catalogues'
        ));
    }

    public function create() {
        $this->authorize('modules', '{module}.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.{module}Catalogue');
        $config['method'] = 'create';
        $template = 'backend.{module}.catalogue.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(Store{Module}CatalogueRequest $request) {
        if ($this->{module}CatalogueService->create($request, $this->language)) {
            return redirect() -> route('{module}.catalogue.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('{module}.catalogue.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', '{module}.catalogue.update');
        $config = $this->configData();
        $template = 'backend.{module}.catalogue.store';
        $config['seo'] = __('messages.{module}Catalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        ${module}Catalogue = $this->{module}CatalogueRepository->get{Module}CatalogueById($id, $this->language);
        $album = json_decode(${module}Catalogue->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}Catalogue',
            'dropdown',
            'album'
        ));
    }

    public function update($id, Update{Module}CatalogueRequest $request) {
        if ($this->{module}CatalogueService->update($id, $request, $this->language)) {
            return redirect() -> route('{module}.catalogue.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('{module}.catalogue.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', '{module}.catalogue.destroy');
        ${module}Catalogue = $this->{module}CatalogueRepository->get{Module}CatalogueById($id, $this->language);
        $config['seo'] = __('messages.{module}Catalogue');
        $template = 'backend.{module}.catalogue.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            '{module}Catalogue',
            'config'
        ));
    }

    public function destroy($id, Delete{Module}CatalogueRequest $request) {
        if ($this->{module}CatalogueService->delete($id, $this->language)) {
            return redirect() -> route('{module}.catalogue.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('{module}.catalogue.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function initialize() {
        $this->nestedSet = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
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
