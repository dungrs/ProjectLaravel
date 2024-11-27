<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\SlideService;
use App\Repositories\SlideRepository;

use App\Http\Request\StoreSlideRequest;
use App\Http\Request\UpdateSlideRequest;

use App\Models\Language;

class SlideController extends Controller
{   
    protected $slideService;
    protected $slideRepository;
    protected $language;
    
    public function __construct(SlideService $slideService, SlideRepository $slideRepository) {
        $this->slideService = $slideService;
        $this->slideRepository = $slideRepository;

        $this->middleware(function($request, $next) {
            // Lấy ra ngôn ngữ hiện tại     
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            // Sau khi xử lý xong nó sẽ truyền $request tới các middlewere khác để xử lý nốt phần còn lại
            // Rồi mới đến phần Controller
            return $next($request);
        });
    }
    public function index(Request $request) {
        $this->authorize('modules', 'slide.index');
        $slides = $this->slideService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Slide'
        ];
        $config['seo'] = __('messages.slide');
        $template = 'backend.slide.slide.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slides'
        ));
    }

    public function create() {
        $this->authorize('modules', 'slide.create');
        $config = $this->config();
        $config['seo'] = __('messages.slide');
        $config['method'] = 'create';
        $template = 'backend.slide.slide.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSlideRequest $request) {
        if ($this->slideService->create($request, $this->language)) {
            return redirect() -> route('slide.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('slide.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'slide.update');
        $config = $this->config();
        $template = 'backend.slide.slide.store';
        $config['seo'] = __('messages.slide');
        $config['method'] = 'edit';
        $slide = $this->slideRepository->findById($id);
        $slideItem = $this->slideService->convertSlideArray($slide->item[$this->language]);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slide',
            'slideItem'
        ));
    }

    public function update($id, UpdateSlideRequest $request) {
        if ($this->slideService->update($id, $request, $this->language)) {
            return redirect() -> route('slide.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('slide.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'slide.destroy');
        $slide = $this->slideRepository->findById($id);
        $config['seo'] = __('messages.slide');
        $template = 'backend.slide.slide.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'slide',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->slideService->delete($id)) {
            return redirect() -> route('slide.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('slide.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/library/slide.js'
            ]
        ];
    }

}
