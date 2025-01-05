<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SystemRepository;
use App\Repositories\RouterRepository;

class ProductController extends FrontendController
{   
    protected $routerRepository;

    public function __construct(    
        RouterRepository $routerRepository,
        SystemRepository $systemRepository,
    ) {
        parent::__construct($systemRepository);
        $this->routerRepository = $routerRepository;
    }

    public function index(string $canonical = '') {
        echo 123; die();
    }

    public function config() {
        
    }
}
