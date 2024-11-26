<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\MenuRepository;
use App\Services\MenuCatalogueService;
use App\Services\MenuService;
use App\Http\Request\StoreMenuCatalogueRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class MenuController extends Controller
{   
    protected $menuRepository;
    protected $menuService;
    protected $menuCatalogueService;
    protected $language;
    protected $nestedSet;

    public function __construct(MenuRepository $menuRepository, MenuCatalogueService $menuCatalogueService, MenuService $menuService) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->menuService = $menuService;
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

    public function createCatalogue(StoreMenuCatalogueRequest $request) {
        $menuCatalogue = $this->menuCatalogueService->create($request);
        if ($menuCatalogue !== false) {
            return response()->json([
                'code' => 0,
                'messages' => 'Tạo nhóm menu thành công!',
                'data' => $menuCatalogue,
            ]);
        } else {
            return response()->json([
                'messages' => 'Có vấn đè xảy ra, hãy thử lại',
                'code' => 1
            ]);
        }
    }

    public function drag(Request $request) {
        $json = json_decode($request->string('json'), true);
        $menuCatalogueId = $request->integer("menu_catalogue_id");
        $flag = $this->menuService->dragUpdate($json, 0, $menuCatalogueId, $this->language);
    
        return response()->json([
            'success' => $flag
        ]);
    }

}
