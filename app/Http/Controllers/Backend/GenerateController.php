<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\GenerateService;
use App\Repositories\GenerateRepository;

use App\Http\Request\UpdateGenerateRequest;
use App\Http\Request\StoreGenerateRequest;
use App\Http\Request\TranslateRequest;

class GenerateController extends Controller
{   
    protected $generateService;
    protected $generateRepository;
    
    public function __construct(GenerateService $generateService, GenerateRepository $generateRepository) {
        $this->generateService = $generateService;
        $this->generateRepository = $generateRepository;
    }

    public function index(Request $request) {
        $this->authorize('modules', 'generate.index');
        $generates = $this->generateService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Generate',
        ];
        $config['seo'] = __('messages.generate');
        $template = 'backend.generate.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generates'
        ));
    }

    public function create() {
        $this->authorize('modules', 'generate.create');
        $config = $this->configData();
        $config['seo'] = __('messages.generate');
        $config['method'] = 'create';
        $template = 'backend.generate.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreGenerateRequest $request) {
        if ($this->generateService->create($request)) {
            return redirect() -> route('generate.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('generate.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'generate.update');
        $config = $this->configData();
        $template = 'backend.generate.store';
        $config['seo'] = __('messages.generate');
        $config['method'] = 'edit';
        $generate = $this->generateRepository->findById($id);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generate'
        ));
    }

    public function update($id, UpdateGenerateRequest $request) {
        if ($this->generateService->update($id, $request)) {
            return redirect() -> route('generate.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('generate.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'generate.destroy');
        $language = $this->generateRepository->findById($id);
        $config['seo'] = __('messages.generate');
        $template = 'backend.generate.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'language',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->generateService->delete($id)) {
            return redirect() -> route('generate.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('generate.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js'
            ]
        ];
    }
}
