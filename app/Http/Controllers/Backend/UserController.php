<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\UserService as UserService;
use App\Repositories\ProvinceRepository as ProvinceRepository;
use App\Repositories\UserCatalogueRepository as UserCatalogueRepository;
use App\Repositories\UserRepository as UserRepository;

use App\Http\Request\StoreUserRequest;
use App\Http\Request\UpdateUserRequest;

class UserController extends Controller
{   
    protected $userService;
    protected $provinceRepository;
    protected $userRepository;
    protected $userCatalogueRepository;
    
    public function __construct(UserService $userService, ProvinceRepository $provinceRepository, UserRepository $userRepository, UserCatalogueRepository $userCatalogueRepository) {
        $this->userService = $userService;
        $this->provinceRepository = $provinceRepository;
        $this->userRepository = $userRepository;
        $this->userCatalogueRepository = $userCatalogueRepository;
    }

    public function index(Request $request) {
        $this->authorize('modules', 'user.index');
        $users = $this ->userService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'User'
        ];
        $config['seo'] = config('apps.user');
        $template = 'backend.user.user.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'users'
        ));
    }

    public function create() {
        $this->authorize('modules', 'user.create');
        $config = $this->config();
        $config['seo'] = config('apps.user');
        $config['method'] = 'create';
        $provinces = $this->provinceRepository->all();
        $userCatalogues = $this->userCatalogueRepository->all();
        $template = 'backend.user.user.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'userCatalogues'
        ));
    }

    public function store(StoreUserRequest $request) {
        if ($this->userService->create($request)) {
            return redirect() -> route('user.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('user.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'user.update');
        $config = $this->config();
        $template = 'backend.user.user.store';
        $config['seo'] = config('apps.user');
        $config['method'] = 'edit';
        $provinces = $this->provinceRepository->all();
        $userCatalogues = $this->userCatalogueRepository->all();
        $user = $this->userRepository->findById($id);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'user',
            'userCatalogues'
        ));
    }

    public function update($id, UpdateUserRequest $request) {
        if ($this->userService->update($id, $request)) {
            return redirect() -> route('user.index') -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('user.index') -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'user.destroy');
        $user = $this->userRepository->findById($id);
        $config['seo'] = config('apps.user');
        $template = 'backend.user.user.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'user',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->userService->delete($id)) {
            return redirect() -> route('user.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('user.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function config() {
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
