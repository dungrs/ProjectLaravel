<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\LanguageService as LanguageService;
use App\Repositories\LanguageRepository as LanguageRepository;

use App\Http\Request\UpdateLanguageRequest;
use App\Http\Request\StoreLanguageRequest;
use App\Http\Request\TranslateRequest;

class LanguageController extends Controller
{   
    protected $languageService;
    protected $languageRepository;
    
    public function __construct(LanguageService $languageService, LanguageRepository $languageRepository) {
        $this->languageService = $languageService;
        $this->languageRepository = $languageRepository;
    }

    public function index(Request $request) {
        $this->authorize('modules', 'language.index');
        $languagesList = $this->languageService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Language',
        ];
        $config['seo'] = config('apps.language');
        $template = 'backend.language.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'languagesList'
        ));
    }

    public function create() {
        $this->authorize('modules', 'language.create');
        $config = $this->configData();
        $config['seo'] = config('apps.language');
        $config['method'] = 'create';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreLanguageRequest $request) {
        if ($this->languageService->create($request)) {
            return redirect() -> route('language.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('language.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'language.update');
        $config = $this->configData();
        $template = 'backend.language.store';
        $config['seo'] = config('apps.language');
        $config['method'] = 'edit';
        $language = $this->languageRepository->findById($id);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language'
        ));
    }

    public function update($id, UpdateLanguageRequest $request) {
        if ($this->languageService->update($id, $request)) {
            return redirect() -> route('language.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('language.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'language.destroy');
        $language = $this->languageRepository->findById($id);
        $config['seo'] = config('apps.language');
        $template = 'backend.language.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'language',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->languageService->delete($id)) {
            return redirect() -> route('language.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('language.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function switchBackendLanguage($id) {
        $language = $this->languageRepository->findById($id);
        if ($this->languageService->switch($id)) {
            session(['app_locale' => $language->canonical]);
            app()->setLocale($language->canonical); // Sử dụng helper app()
        }
        return back();
    }

    public function translate($id = 0, $languageId = 0, $model = '') {
        $repositoryInstance = $this->repositoryInstace($model);
        $languageInstace = $this->repositoryInstace('Language');
        $currentLanguage = $languageInstace->findByCondition(
            [
                ['canonical', '=', session('app_locale')]
            ]
        );

        $method = 'get'. $model .'ById';
        $object = $repositoryInstance->{$method}($id, $currentLanguage->id);
        $objectTranslate = $repositoryInstance->{$method}($id, $languageId);
        $this->authorize('modules', 'language.translate');
        $option = [
            'id' => $id,
            'languageId' => $languageId,
            'model' => $model
        ];
        $config = [
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
        $config['seo'] = config('apps.language');
        $config['method'] = 'create';
        $template = 'backend.language.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'object',
            'objectTranslate',
            'option'
        ));
    }

    public function storeTranslate(TranslateRequest $request) {
        $option = $request->input('option');
        if ($this->languageService->saveTranslate($option ,$request)) {
            return redirect()->back()->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()->back()->with('error', 'Có vấn đề xảy ra hãy thử lại');
    }

    private function configData() {
        return [
            'js' => [
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js'
            ]
        ];
    }

    private function repositoryInstace($model) {
        $repositoryNamespace = "\App\Repositories\\" . ucfirst($model) . "Repository";
        if (class_exists($repositoryNamespace)) {
            $repositoryInstance = app($repositoryNamespace);
        }

        return $repositoryInstance ?? null;
    }
}
