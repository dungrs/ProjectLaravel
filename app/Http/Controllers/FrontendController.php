<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class FrontendController extends Controller
{   
    protected $language;

    protected $nestedSet;

    public function __construct() {
        $this->middleware(function($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }
}
