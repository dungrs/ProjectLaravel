<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SystemRepository;
use App\Repositories\RouterRepository;
use Illuminate\Http\Request;

class RouterController extends FrontendController
{   
    protected $routerRepository;

    public function __construct(    
        RouterRepository $routerRepository,
        SystemRepository $systemRepository,
    ) {
        parent::__construct($systemRepository);
        $this->routerRepository = $routerRepository;
    }

    public function index(string $canonical = '', int $page = 1,  Request $request) {
        $router = $this->routerRepository->findByCondition([
            ['canonical', '=', $canonical],
            ['language_id', '=', $this->language]
        ]);
    
        if (!$router) {
            abort(404, 'Router not found');
        }
    
        $controllerClass = $router->controllers;
    
        if (!class_exists($controllerClass)) {
            abort(500, "Controller {$controllerClass} does not exist");
        }
    
        $controller = app($controllerClass);
    
        if (!method_exists($controller, 'index')) {
            abort(500, "Method 'index' not found in controller {$controllerClass}");
        }
    
        return $controller->index($request, $page, $router->module_id);
    }

}
