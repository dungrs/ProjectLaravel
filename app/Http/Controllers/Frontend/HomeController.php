<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;

class HomeController extends FrontendController
{   

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $config = $this->config();
        return view('frontend.homepage.home.index', compact(
            'config',
        ));
    }

    private function config() {
        return [];
    }

}
