<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\PostCatalogueService as PostCatalogueService;
use App\Repositories\PostCatalogueRepository as PostCatalogueRepository;

use App\Models\Language;

use App\Http\Request\UpdatePostCatalogueRequest;
use App\Http\Request\StorePostCatalogueRequest;
use App\Http\Request\DeletePostCatalogueRequest;

use App\Classes\Nestedsetbie;

class PostCatalogueController extends Controller
{   
    protected $postCatalogueService;
    protected $postCatalogueRepository;
    protected $nestedSet;
    protected $language;
    
    public function __construct(PostCatalogueService $postCatalogueService, PostCatalogueRepository $postCatalogueRepository) {
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
        $this->postCatalogueService = $postCatalogueService;
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this ->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', 'post.catalogue.index');
        $postCatalogues = $this->postCatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'PostCatalogue'
        ];
        $config['seo'] = __("messages.postCatalogue");
        $template = 'backend.post.catalogue.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'postCatalogues'
        ));
    }

    public function create() {
        $this->authorize('modules', 'post.catalogue.create');
        $config = $this->configData();
        $config['seo'] = __('messages.postCatalogue');
        $config['method'] = 'create';
        $template = 'backend.post.catalogue.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StorePostCatalogueRequest $request) {
        if ($this->postCatalogueService->create($request, $this->language)) {
            return redirect() -> route('post.catalogue.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('post.catalogue.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'post.catalogue.update');
        $config = $this->configData();
        $template = 'backend.post.catalogue.store';
        $config['seo'] = __('messages.postCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
        $album = json_decode($postCatalogue->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'postCatalogue',
            'dropdown',
            'album'
        ));
    }

    public function update($id, UpdatePostCatalogueRequest $request) {
        if ($this->postCatalogueService->update($id, $request, $this->language)) {
            return redirect() -> route('post.catalogue.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('post.catalogue.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'post.catalogue.destroy');
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
        $config['seo'] = __('messages.postCatalogue');
        $template = 'backend.post.catalogue.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'postCatalogue',
            'config'
        ));
    }

    public function destroy($id, DeletePostCatalogueRequest $request) {
        if ($this->postCatalogueService->delete($id, $this->language)) {
            return redirect() -> route('post.catalogue.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('post.catalogue.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function initialize() {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
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
