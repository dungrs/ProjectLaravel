<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\PostService as PostService;
use App\Repositories\PostRepository as PostRepository;

use App\Models\Language;

use App\Http\Request\UpdatePostRequest;
use App\Http\Request\StorePostRequest;

use App\Classes\Nestedsetbie;

class PostController extends Controller
{   
    protected $postService;
    protected $postRepository;
    protected $nestedSet;
    protected $language;
    
    
    public function __construct(
        PostService $postService, 
        PostRepository $postRepository,
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

        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->initialize();
    }

    public function index(Request $request) {
        $this->authorize('modules', 'post.index');
        $posts = $this->postService->paginate($request, $this->language);
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
            'model' => 'Post',
        ];
        $config['seo'] = __('messages.post');
        $template = 'backend.post.post.index';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'posts',
            'dropdown',
            'languageSelectId'
        ));
    }

    public function create() {
        $this->authorize('modules', 'post.create');
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'create';
        $template = 'backend.post.post.store';
        $dropdown = $this->nestedSet->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StorePostRequest $request) {
        if ($this->postService->create($request, $this->language)) {
            return redirect() -> route('post.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('post.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'post.update');
        $config = $this->configData();
        $template = 'backend.post.post.store';
        $config['seo'] = __('messages.post');
        $config['method'] = 'edit';
        $dropdown = $this->nestedSet->Dropdown();
        $post = $this->postRepository->getPostById($id, $this->language);
        $album = json_decode($post->album);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'post',
            'dropdown',
            'album'
        ));
    }

    public function update($id, UpdatePostRequest $request) {
        if ($this->postService->update($id, $request, $this->language)) {
            return redirect() -> route('post.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('post.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'post.destroy');
        $post = $this->postRepository->getPostById($id, $this->language);
        $config['seo'] = __('messages.post');
        $template = 'backend.post.post.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'post',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->postService->delete($id)) {
            return redirect() -> route('post.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('post.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
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
