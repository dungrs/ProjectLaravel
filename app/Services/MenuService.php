<?php

namespace App\Services;
use App\Services\Interfaces\MenuServiceInterface;
use App\Repositories\MenuRepository;
use App\Repositories\MenuCatalogueRepository;
use App\Repositories\RouterRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;


/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{   
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $nestedSet;
    protected $routerRepository;

    public function __construct(MenuRepository $menuRepository, MenuCatalogueRepository $menuCatalogueRepository, RouterRepository $routerRepository) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request) {
        return [];
    }

    public function save($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->only('menu', 'menu_catalogue_id');
            if (count($payload['menu']['name'])) {
                foreach($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [ 
                        'order' => $payload['menu']['order'][$key],
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'user_id' => Auth::id()
                    ];

                    if ($menuId == 0) {
                        $menuSave = $this->menuRepository->create($menuArray);
                    } else {
                        $menuSave = $this->menuRepository->updateAndGetData($menuId, $menuArray);
                        if ($menuSave->rgt - $menuSave->lft > 1) {
                            $this->menuRepository->updateByWhere(
                                [   
                                    ['lft', '>', $menuSave->lft],
                                    ['rgt', '<', $menuSave->rgt],
                                ],
                                ['menu_catalogue_id' => $payload['menu_catalogue_id']]
                            );
                        }
                    }

                    if ($menuSave->id > 0) {
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'menu_id' => $menuSave->id,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];
                        $this->menuRepository->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }
                $this->initialize($languageId);
                $this->nestedSet();
            }

            
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function saveChildren($request, $languageId, $menu) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->only('menu');
            if (count($payload['menu']['name'])) {
                foreach($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'parent_id' => $menu->id,
                        'order' => $payload['menu']['order'][$key],
                        'menu_catalogue_id' => $menu->menu_catalogue_id,
                        'user_id' => Auth::id()
                    ];

                    if ($menuId == 0) {
                        $menuSave = $this->menuRepository->create($menuArray);
                    } else {
                        $menuSave = $this->menuRepository->updateAndGetData($menuId, $menuArray);
                    }
                    if ($menuSave->id > 0) {
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'menu_id' => $menuSave->id,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];
                        $this->menuRepository->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }
                $this->initialize($languageId);
                $this->nestedSet();
            }

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function getAndConvertMenu($id, $languageId) : array {
        $menuList = $this->menuRepository->findByCondition(
            [
                ['parent_id', '=', $id],
                ['language_id', '=', $languageId]
            ],
            true, // Lấy nhiều kết quả
            [
                [
                    'table' => 'menu_language', // Bảng liên kết
                    'on' => ['menu_language.menu_id', 'menus.id'] // Điều kiện join
                ]
            ]
        );

        $menuData = [
            'name' => [],
            'canonical' => [],
            'order' => [],
            'id' => []
        ];

        foreach ($menuList as $menu) {
            $menuData['name'][] = $menu->name;
            $menuData['canonical'][] = $menu->canonical;
            $menuData['order'][] = $menu->order;
            $menuData['id'][] = $menu->id;
        }

        return $menuData;
    }

    public function dragUpdate(array $menus = [], $parentId = 0, $menuCatalogueId = 0, $languageId, $order = 1) {
        foreach ($menus as $menu) {
            $payload = [
                'parent_id' => $parentId,
                'menu_catalogue_id' => $menuCatalogueId,
                'order' => $order // Gán order cho menu
            ];
    
            $updatedMenu = $this->menuRepository->updateAndGetData($menu['id'], $payload);
            if (!$updatedMenu) {
                return false; // Nếu không thành công, trả về false
            }
    
            if (isset($menu['children']) && count($menu['children']) > 0) {
                // Order được reset về 1 cho các menu con
                if (!$this->dragUpdate($menu['children'], $menu['id'], $menuCatalogueId, $languageId, 1)) {
                    return false; // Nếu một trong các cập nhật con không thành công, trả về false
                }
            }
    
            $order++;
        }
    
        $this->initialize($languageId);
        $this->nestedSet();
    
        return true; // Trả về true nếu tất cả đều thành công
    }
    
    public function update($id, $request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send'); 
            // $this->menuRepository->update($id, $payload);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function findMenuUtemTranslate($menus, int $currentLanguage = 1, int $languageId = 1, int $menuCatalogueId = 1) {
        $output = [];
        if (count($menus)) {
            foreach($menus as $menu) {
                $canonical = $menu->canonical;
                $routers = $this->routerRepository->findByCondition([
                    ['canonical', '=', $canonical],
                ]);
                if (!is_null($routers) || (is_object($routers) && method_exists($routers, 'isEmpty'))) {
                    $controllers = explode('\\', $routers->controllers);
                    $model = str_replace('Controller', '', end($controllers));
                
                    $repositoryInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';
            
                    if (!class_exists($repositoryInterfaceNamespace)) {
                        return response()->json(['error' => 'Repository not found.'], 404);
                    }
                    
                    $repositoryInterface = app($repositoryInterfaceNamespace);
                    $alias = Str::snake($model) . '_language';

                    $object = $repositoryInterface->findByWhereHas(
                        [
                            ['language_id', '=', $languageId],
                            ['canonical', '=', $canonical]
                        ], 
                        'languages', 
                        $alias
                    );
                    if ($object) {
                        $translateObject = $object->languages()->where('language_id', $currentLanguage)->first([$alias. '.name', $alias.'.canonical']);
                        if (!is_null($translateObject)) {
                            $menu->translate_name = $translateObject->name;
                            $menu->translate_canonical = $translateObject->canonical;
                        } else {
                            $translateObject = $menu->languages()
                            ->where('language_id', $currentLanguage)
                            ->where('menu_catalogue_id', $menuCatalogueId)
                            ->join('menus', 'menus.id', '=', 'menu_language.menu_id')
                            ->join('menu_catalogues', 'menu_catalogues.id', '=', 'menus.menu_catalogue_id') // Kết nối bảng `menu_catalogues`
                            ->first([
                                'menu_language.name', 
                                'menu_language.canonical',
                            ]);
                            if (!is_null($translateObject)) {
                                dd($translateObject);
                                $menu->translate_name = $translateObject->name;
                                $menu->translate_canonical = $translateObject->canonical;
                            }
                        }
                    }
                    } 

                $output[] = $menu;
            }
        }

        return $output;
    }

    public function saveTranslateMenu($request, $languageId) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $payload = $request->only('translate');

            if (isset($payload['translate']['name']) && count($payload['translate']['name']) > 0) {
                foreach ($payload['translate']['name'] as $key => $val) {
                    if ($val === null) {
                        continue;
                    }

                    if (!isset($payload['translate']['canonical'][$key], $payload['translate']['id'][$key])) {
                        continue; // Bỏ qua nếu không đủ dữ liệu
                    }

                    $temp = [
                        'language_id' => $languageId,
                        'name' => $val,
                        'canonical' => $payload['translate']['canonical'][$key],
                        'menu_id' => $payload['translate']['id'][$key]
                    ];

                    $menu = $this->menuRepository->findById($payload['translate']['id'][$key]);

                    if ($menu) {
                        $menu->languages()->detach($languageId); // Đảm bảo $menu không phải là null
                        $this->menuRepository->createPivot($menu, $temp, 'languages');
                    }
                }
            }

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $this->menuRepository->deleteByCondition([
                ['menu_catalogue_id', '=', $id]
            ]);
            $this->menuCatalogueRepository->forceDelete($id);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function initialize($languageId) {
        $this->nestedSet = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => TRUE,
            'language_id' => $languageId,
        ]);
    }
}
