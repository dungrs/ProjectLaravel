<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Services\SystemService;
use App\Repositories\SystemRepository;
use App\Models\Language;

use Illuminate\Http\Request;
use App\Classes\System;

class SystemController extends Controller
{   
    protected $systemService;
    protected $systemRepository;
    protected $systemLibrary;
    protected $languageId;

    public function __construct(SystemService $systemService, SystemRepository $systemRepository, System $systemLibrary) {
        $this->systemService = $systemService;
        $this->systemLibrary = $systemLibrary;
        $this->systemRepository = $systemRepository;
        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->languageId = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới các middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }

    public function index() {
        $template = 'backend.system.index';
        $config = $this->config();
        $config['seo'] = __('messages.system');
        $systemConfig = $this->systemLibrary->config();
        $systems = convert_array($this->systemRepository->findByCondition([['language_id', '=', $this->languageId]], true), 'keyword', 'content');

        // Sử dụng hàm compact để chuyển dữ liệu từ controller sang view
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'systems'
        ));
    }

    public function store(Request $request) {
        if ($this->systemService->save($request, $this->languageId)) {
            return redirect() -> route('system.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('system.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function translate($languageId = 1) {
        $template = 'backend.system.index';
        $config = $this->config();
        $config['seo'] = __('messages.system');
        $config['method'] = 'translate';
        $systemConfig = $this->systemLibrary->config();
        $systems = convert_array($this->systemRepository->findByCondition([['language_id', '=', $languageId]], true), 'keyword', 'content');
        // Sử dụng hàm compact để chuyển dữ liệu từ controller sang view
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'languageId',
            'systems'
        ));
    }

    public function saveTranslate(Request $request, $languageId) {
        if ($this->systemService->save($request, $languageId)) {
            return redirect() -> route('system.translate', ['languageId' => $languageId]) -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('system.translate', ['languageId' => $languageId]) -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    private function config() {
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ]
        ];
    }
}
