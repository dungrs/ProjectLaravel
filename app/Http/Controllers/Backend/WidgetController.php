<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\WidgetService;
use App\Repositories\WidgetRepository;

use App\Http\Request\StoreWidgetRequest;
use App\Http\Request\UpdateWidgetRequest;

class WidgetController extends Controller
{   
    protected $widgetService;
    protected $widgetRepository;
    
    public function __construct(WidgetService $widgetService, WidgetRepository $widgetRepository) {
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
    }

    public function index(Request $request) {
        // $this->authorize('modules', 'widget.index');
        $widgets = $this ->widgetService->paginate($request);
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
            'widgets'
        ));
    }

    public function create() {
        // $this->authorize('modules', 'widget.create');
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
        if ($this->widgetService->create($request)) {
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
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
        ));
    }

    public function update($id, UpdateWidgetRequest $request) {
        if ($this->widgetService->update($id, $request)) {
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

    public function config() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/finder.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/plugin/ckeditor/ckeditor.js',
            ]
        ];
    }

}
