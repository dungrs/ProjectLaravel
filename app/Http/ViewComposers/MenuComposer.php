<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\MenuCatalogueRepository;

class MenuComposer {

    protected $menuCatalogueRepository;
    protected $language;

    public function __construct(MenuCatalogueRepository $menuCatalogueRepository, $language) {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->language = $language;
    }

    public function compose(View $view) {
        $menuCatalogueItems = $this->menuCatalogueRepository->all();
        $menus = [];
        foreach ($menuCatalogueItems as $menuCatalogueItem) {
            $menuCatalogues = $this->menuCatalogueRepository->findByCondition(
                [
                    ['menu_catalogues.keyword', '=', $menuCatalogueItem->keyword],
                    ['menu_language.language_id', '=', $this->language],
                    ['menu_catalogues.publish', '=', 2]
                ],
                true,
                [
                    [
                        'table' => 'menus', // Bảng liên kết
                        'on' => ['menus.menu_catalogue_id', 'menu_catalogues.id'] // Điều kiện join
                    ],
                    [
                        'table' => 'menu_language', // Bảng liên kết
                        'on' => ['menu_language.menu_id', 'menus.id'] // Điều kiện join
                    ]
                ],
                
                ['menus.order' => 'DESC'],
                ['menus.*', 'menu_language.*', 'menu_catalogues.name as menu_catalogue_name', 'menu_catalogues.publish']
            );
            $menus[$menuCatalogueItem->keyword] = recursive($menuCatalogues);
        }

        $view->with('menus', $menus);
    }
}
