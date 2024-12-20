<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Request\StoreMenuChildrenRequest;
use Illuminate\Http\Request;

use App\Services\MenuService;
use App\Repositories\MenuCatalogueRepository;
use App\Services\MenuCatalogueService;
use App\Repositories\MenuRepository;
use App\Repositories\LanguageRepository;

use App\Http\Request\StoreMenuRequest;

use App\Models\Language;

class MenuController extends Controller
{   
    protected $menuService;
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $menuCatalogueService;
    protected $language;
    protected $languageRepository;
    
    public function __construct(
        MenuService $menuService, 
        MenuRepository $menuRepository, 
        MenuCatalogueRepository $menuCatalogueRepository, 
        MenuCatalogueService $menuCatalogueService,
        LanguageRepository $languageRepository
        ) {
        $this->menuService = $menuService;
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuCatalogueService = $menuCatalogueService;
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
        $this->authorize('modules', 'menu.index');
        $menuCatalogues = $this->menuCatalogueService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ], 
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'MenuCatalogue'
        ];
        $config['seo'] = __("messages.menu");
        $template = 'backend.menu.menu.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogues'
        ));
    }

    public function create() {
        $this->authorize('modules', 'menu.create');
        $config = $this->config();
        $config['seo'] = __("messages.menu");
        $config['method'] = 'create';
        $template = 'backend.menu.menu.store';
        $menuCatalogues = $this->menuCatalogueRepository->all();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogues'
        ));
    }

    public function store(StoreMenuRequest $request) {
        if ($this->menuService->save($request, $this->language)) {
            return redirect() -> route('menu.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('menu.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function edit($id) {
        $this->authorize('modules', 'menu.update');
        $config = $this->config();
        $template = 'backend.menu.menu.show';
        $config['seo'] = __("messages.menu");
        $config['method'] = 'edit';
        $menus = $this->menuRepository->findByCondition(
            [
                ['menu_catalogue_id', '=', $id],
                ['language_id', '=', $this->language]
            ],
            true,
            [
                [
                    'table' => 'menu_language', // Bảng liên kết
                    'on' => ['menu_language.menu_id', 'menus.id'] // Điều kiện join
                ],
                [
                    'table' => 'menu_catalogues', // Bảng liên kết
                    'on' => ['menu_catalogues.id', 'menus.menu_catalogue_id'] // Điều kiện join
                ]
            ],
            
            ['menus.order' => 'ASC'],
            ['menus.*', 'menu_language.*', 'menu_catalogues.name as menu_catalogue_name']
        );
        
        $menuList = !empty($menus) ? recursive($menus, 0) : [];
        if (empty($menuList)) {
            $menus = $this->menuCatalogueRepository->findById($id);
        }
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menus',
            'menuList'
        ));
    }

    public function update(Request $request) {
        if ($this->menuService->save($request, $this->language)) {
            return redirect() -> route('menu.edit', $request->input('menu_catalogue_id')) -> with('success', 'Cập nhật bản ghi thành công');
        } else {
            return redirect() -> route('menu.edit', $request->input('menu_catalogue_id')) -> with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
        }
    }

    public function delete($id) {
        $this->authorize('modules', 'menu.destroy');
        $menuCatalogue = $this->menuCatalogueRepository->findById($id);
        $config['seo'] = __("messages.menu");
        $template = 'backend.menu.menu.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'menuCatalogue',
            'config'
        ));
    }

    public function destroy($id) {
        if ($this->menuService->delete($id)) {
            return redirect() -> route('menu.index') -> with('success', 'Xóa bản ghi thành công');
        } 
        return redirect() -> route('menu.index') -> with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function children($id) {
        $this->authorize('modules', 'menu.create');
        $menu = $this->menuRepository->findByCondition(
            [
                ['menu_id', '=', $id],
                ['language_id', '=', $this->language]
            ],
            false,
            [
                [
                    'table' => 'menu_language', // Bảng liên kết
                    'on' => ['menu_language.menu_id', 'menus.id'] // Điều kiện join
                ],
            ]
        );

        $menuList = $this->menuService->getAndConvertMenu($id, $this->language);
        $config = $this->config();
        $config['seo'] = __("messages.menu");
        $config['method'] = 'children';
        $template = 'backend.menu.menu.children';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menu',
            'menuList'
        ));
    }

    public function saveChildren(StoreMenuChildrenRequest $request, $id) {
        $menu = $this->menuRepository->findById($id);
        if ($this->menuService->saveChildren($request, $this->language, $menu)) {
            return redirect() -> route('menu.edit') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('menu.edit') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function editMenu($id) {
        $this->authorize('modules', 'menu.update');
        $menu = $this->menuRepository->findByCondition(
            [
                ['menu_catalogue_id', '=', $id],
                ['parent_id', '=', 0],
                ['language_id', '=', $this->language]
            ],
            false,
            [
                [
                    'table' => 'menu_language', // Bảng liên kết
                    'on' => ['menu_language.menu_id', 'menus.id'] // Điều kiện join
                ],
            ],
        );
        $menuList = $this->menuService->getAndConvertMenu(0, $this->language);
        $config = $this->config();
        $config['seo'] = __("messages.menu");
        $config['method'] = 'update';
        $template = 'backend.menu.menu.store';
        $menuCatalogues = $this->menuCatalogueRepository->all();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menu',
            'menuList',
            'menuCatalogues'
        ));
    }

    public function translate($id, $languageId) {
        $config = $this->config();
        $config['seo'] = __("messages.menu");
        $config['method'] = 'translate';
        $template = 'backend.menu.menu.translate';

        $menus = $this->menuCatalogueRepository->findById($id);
        $language = $this->languageRepository->findById($languageId);
        $menuItems = $this->menuRepository->findByCondition(
            [
                ['menu_catalogue_id', '=', $id],
                ['language_id', '=', $this->language]
            ],
            true,
            [
                [
                    'table' => 'menu_language', // Bảng liên kết
                    'on' => ['menu_language.menu_id', 'menus.id'] // Điều kiện join
                ],
            ],
            ['lft' => 'ASC'],
        );
        $menuBuildItems = buildMenu($this->menuService->findMenuUtemTranslate($menuItems, $languageId, $this->language, $menus->id));
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menus',
            'language',
            'menuBuildItems'
        ));
    }

    public function saveTranslate(Request $request, $languageId) {
        if ($this->menuService->saveTranslateMenu($request, $languageId)) {
            return redirect() -> route('menu.index') -> with('success', 'Thêm mới bản ghi thành công');
        } else {
            return redirect() -> route('menu.index') -> with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
        }
    }

    public function config() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/js/plugins/nestable/jquery.nestable.js',
                'backend/library/library.js',
                'backend/library/menu.js',
            ]
        ];
    }

}
