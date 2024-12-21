<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\SourceService;
use App\Repositories\SourceRepository;
use App\Repositories\LanguageRepository;

use App\Http\Request\Source\StoreSourceRequest;
use App\Http\Request\Source\UpdateSourceRequest;

use App\Models\Language;

class SourceController extends Controller
{   
    protected $sourceService;
    protected $sourceRepository;
    protected $languageRepository;
    protected $language;
    
    public function __construct(SourceService $sourceService, SourceRepository $sourceRepository, LanguageRepository $languageRepository) {
        $this->sourceService = $sourceService;
        $this->sourceRepository = $sourceRepository;
        $this->languageRepository = $languageRepository;

        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới cấc middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }

    public function index(Request $request) {
        $this->authorize('modules', 'source.index');
        $sources = $this->sourceService->paginate($request);
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
            'model' => 'Source'
        ];
        $config['seo'] = __('messages.source');
        $template = 'backend.source.source.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'sources',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', 'source.create');
        $config = $this->config();
        $config['seo'] = __('messages.source');
        $config['method'] = 'create';
        $template = 'backend.source.source.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSourceRequest $request) {
        if ($this->sourceService->create($request)) {
            return redirect() -> route('source.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('source.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'source.update');
        $config = $this->config();
        $template = 'backend.source.source.store';
        $config['seo'] = __('messages.source');
        $config['method'] = 'edit';
        $source = $this->sourceRepository->findById($id);
        $album_json = json_encode($source->album);
        $album = json_decode($album_json);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'source',
            'album',
        ));
    }

    public function update($id, UpdateSourceRequest $request) {
        if ($this->sourceService->update($id, $request)) {
            return redirect() -> route('source.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('source.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'source.destroy');
        $source = $this->sourceRepository->findById($id);
        $config['seo'] = __('messages.source');
        $template = 'backend.source.source.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'source',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->sourceService->delete($id)) {
            return redirect() -> route('source.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('source.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function config() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/source.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ]
        ];
    }

}
