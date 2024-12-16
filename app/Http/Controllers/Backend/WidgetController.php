<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\WidgetService;
use App\Repositories\WidgetRepository;
use App\Repositories\LanguageRepository;

use App\Http\Request\StoreWidgetRequest;
use App\Http\Request\UpdateWidgetRequest;

use App\Models\Language;

class WidgetController extends Controller
{   
    protected $widgetService;
    protected $widgetRepository;
    protected $languageRepository;
    protected $language;
    
    public function __construct(WidgetService $widgetService, WidgetRepository $widgetRepository, LanguageRepository $languageRepository) {
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
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
        $this->authorize('modules', 'widget.index');
        $widgets = $this ->widgetService->paginate($request);
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
            'model' => 'Widget'
        ];
        $config['seo'] = __('messages.widget');
        $template = 'backend.widget.widget.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widgets',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', 'widget.create');
        $config = $this->config();
        $config['seo'] = __('messages.widget');
        $config['method'] = 'create';
        $template = 'backend.widget.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreWidgetRequest $request) {
        if ($this->widgetService->create($request, $this->language)) {
            return redirect() -> route('widget.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('widget.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'widget.update');
        $config = $this->config();
        $template = 'backend.widget.widget.store';
        $config['seo'] = __('messages.widget');
        $config['method'] = 'edit';
        $widget = $this->widgetRepository->findById($id);
        $widget->description = $widget->description[$this->language]; // Lấy dữ liệu dạng json
        $widgetItem = $this->widgetService->getWidgetItem($widget->model, $widget->model_id, $this->language);
        $album_json = json_encode($widget->album);
        $album = json_decode($album_json);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'album',
            'widgetItem'
        ));
    }

    public function update($id, UpdateWidgetRequest $request) {
        if ($this->widgetService->update($id, $request, $this->language)) {
            return redirect() -> route('widget.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('widget.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'widget.destroy');
        $widget = $this->widgetRepository->findById($id);
        $config['seo'] = __('messages.widget');
        $template = 'backend.widget.widget.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'widget',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->widgetService->delete($id)) {
            return redirect() -> route('widget.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('widget.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function translate($languageId, $widgetId) {
        $this->authorize('modules', 'widget.translate');
        $config = $this->config();
        $widget = $this->widgetRepository->findById($widgetId);
        $widget->jsonDescription = $widget->description;
        $widget->description = $widget->description[$this->language];

        // Tạo ra 1 lớp mới ảo
        $widgetTranslate = new \stdClass;
        $widgetTranslate->meta_description = ($widget->jsonDescription[$languageId]) ?? '';

        $translate = $this->languageRepository->findById($languageId);
        $config['seo'] = __('messages.widget');
        $config['method'] = 'translate';
        $template = 'backend.widget.widget.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'translate',
            'widgetTranslate'
        ));
    }

    public function saveTranslate(Request $request) {
        $languageId = $request->input('translateId');
        $widgetId = $request->input('widgetId');
        if ($this->widgetService->saveTranslate($request)) {
            return redirect() -> route('widget.translate', ['languageId' => $languageId, 'id' => $widgetId]) -> with('success', 'Tạo bản dịch thành công');
        } 
        return redirect() -> route('widget.translate', ['languageId' => $languageId, 'id' => $widgetId]) -> with('error', 'Tạo bản dịch không thành công. Hãy thử lại');
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
                'backend/library/widget.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ]
        ];
    }

}
