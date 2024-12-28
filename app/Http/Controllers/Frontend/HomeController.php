<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SlideRepository;
use App\Services\WidgetService;
use Illuminate\Http\Request;

class HomeController extends FrontendController
{   

    protected $slideRepository;
    protected $widgetService;

    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService
    ) {
        parent::__construct();
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
    }

    public function index() {
        $config = $this->config();
        $widget = [
            'category' => $this->widgetService->findWidgetByKeyword('category', $this->language, ['children' => true]),
        ];
        $slides = $this->slideRepository->findByCondition(
            [
                config('apps.general.defaultPublish'),
                ['keyword', '=', 'main-slide']
            ],
            false,
            [],
            ['id' => 'desc'],
        );

        dd($widget['category']);
        return view('frontend.homepage.home.index', compact(
            'config',
            'slides'
        ));
    }

    private function config() {
        return [];
    }

}
