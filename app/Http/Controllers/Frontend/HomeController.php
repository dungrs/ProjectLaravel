<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Services\SlideService;
use App\Services\WidgetService;

use App\Repositories\SystemRepository;

use App\Classes\SlideEnum;

class HomeController extends FrontendController
{   

    protected $slideService;
    protected $widgetService;

    public function __construct(
        SlideService $slideService,
        WidgetService $widgetService,
        SystemRepository $systemRepository,
    ) {
        parent::__construct($systemRepository);
        $this->slideService = $slideService;
        $this->widgetService = $widgetService;
    }

    public function index() {
        $config = $this->config();
        $system = $this->getSystem();

        $keywords = [
            'category' => ['keyword' => 'category', 'options' => ['object' => true, 'promotion' => true, 'children' => true, ]],
            'new' => ['keyword' => 'post-catalogue-hl', 'options' => ['object' => true, 'children' => true]],
            'bestseller' => ['keyword' => 'bestseller', 'options' => ['object' => false, 'promotion' => true]],
            'category-hl' => ['keyword' => 'catagory-hl', 'options' => ['object' => true, 'children' => true]],
            'category-home' => ['keyword' => 'category-home', 'options' => ['object' => true, 'promotion' => true, 'children' => true]],
        ];
        
        $widget = $this->widgetService->getWidget($keywords, $this->language);
        $slides = $this->slideService->getSlide([SlideEnum::MAIN, SlideEnum::BANNER], $this->language);
        $system = $this->getSystem();
        $seo = [
            'meta_title' => $system['seo_meta_title'],
            'meta_keyword' => $system['seo_meta_keyword'],
            'meta_description' => $system['seo_meta_description'],
            'meta_image' => $system['seo_meta_image'],
            'canonical' => config('app.url')
        ];

        return view('frontend.homepage.home.index', compact(
            'config',
            'slides',
            'widget',
            'seo',
            'system'
        ));
    }

    private function config() {
        return [];
    }

}
