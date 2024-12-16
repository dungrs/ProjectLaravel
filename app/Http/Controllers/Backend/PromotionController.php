<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\PromotionService;
use App\Repositories\PromotionRepository;
use App\Repositories\LanguageRepository;

use App\Http\Request\StorePromotionRequest;
use App\Http\Request\UpdatePromotionRequest;

use App\Models\Language;

class PromotionController extends Controller
{   
    protected $promotionService;
    protected $promotionRepository;
    protected $languageRepository;
    protected $language;
    
    public function __construct(PromotionService $promotionService, PromotionRepository $promotionRepository, LanguageRepository $languageRepository) {
        $this->promotionService = $promotionService;
        $this->promotionRepository = $promotionRepository;
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
        $this->authorize('modules', 'promotion.index');
        $promotions = $this ->promotionService->paginate($request);
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
            'model' => 'Promotion'
        ];
        $config['seo'] = __('messages.promotion');
        $template = 'backend.promotion.promotion.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotions',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', 'promotion.create');
        $config = $this->config();
        $config['seo'] = __('messages.promotion');
        $config['method'] = 'create';
        $template = 'backend.promotion.promotion.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StorePromotionRequest $request) {
        if ($this->promotionService->create($request, $this->language)) {
            return redirect() -> route('promotion.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('promotion.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'promotion.update');
        $config = $this->config();
        $template = 'backend.promotion.promotion.store';
        $config['seo'] = __('messages.promotion');
        $config['method'] = 'edit';
        $promotion = $this->promotionRepository->findById($id);
        $album_json = json_encode($promotion->album);
        $album = json_decode($album_json);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
            'album',
        ));
    }

    public function update($id, UpdatePromotionRequest $request) {
        if ($this->promotionService->update($id, $request, $this->language)) {
            return redirect() -> route('promotion.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('promotion.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'promotion.destroy');
        $promotion = $this->promotionRepository->findById($id);
        $config['seo'] = __('messages.promotion');
        $template = 'backend.promotion.promotion.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'promotion',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->promotionService->delete($id)) {
            return redirect() -> route('promotion.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('promotion.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/library/promotion.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ]
        ];
    }

}
