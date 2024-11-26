<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\{Module}Service as {Module}Service;
use App\Repositories\{Module}Repository as {Module}Repository;

use App\Models\Language;

use App\Http\Request\Update{Module}Request;
use App\Http\Request\Store{Module}Request;

use App\Classes\Nestedsetbie;

class {Module}Controller extends Controller
{   
    protected ${module}Service;
    protected ${module}Repository;
    protected $nestedSet;
    protected $language;
    
    
    public function __construct(
        {Module}Service ${module}Service, 
        {Module}Repository ${module}Repository,
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

        $this->{module}Service = ${module}Service;
        $this->{module}Repository = ${module}Repository;
        $this->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', '{module}.index');
        ${module}s = $this->{module}Service->paginate($request, $this->language);
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
            'model' => '{Module}',
        ];
        $config['seo'] = __('messages.{module}');
        $template = 'backend.{module}.{module}.index';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}s',
            'dropdown',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', '{module}.create');
        $config = $this->configData();
        $config['seo'] = __('messages.{module}');
        $config['method'] = 'create';
        $template = 'backend.{module}.{module}.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(Store{Module}Request $request) {
        if ($this->{module}Service->create($request, $this->language)) {
            return redirect() -> route('{module}.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('{module}.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', '{module}.update');
        $config = $this->configData();
        $template = 'backend.{module}.{module}.store';
        $config['seo'] = __('messages.{module}');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        ${module} = $this->{module}Repository->get{Module}ById($id, $this->language);
        $album = json_decode(${module}->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}',
            'dropdown',
            'album'
        ));
    }

    public function update($id, Update{Module}Request $request) {
        if ($this->{module}Service->update($id, $request, $this->language)) {
            return redirect() -> route('{module}.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('{module}.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', '{module}.destroy');
        ${module} = $this->{module}Repository->get{Module}ById($id, $this->language);
        $config['seo'] = __('messages.{module}');
        $template = 'backend.{module}.{module}.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            '{module}',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->{module}Service->delete($id)) {
            return redirect() -> route('{module}.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('{module}.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
