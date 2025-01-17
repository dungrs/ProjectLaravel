<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\SystemRepository;
use App\Models\Language;

class FrontendController extends Controller
{   
    protected $language;
    protected $languageRepository;
    protected $systemRepository;
    protected $nestedSet;

    public function __construct(SystemRepository $systemRepository) {
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first() ?? app('App\\Repositories\\LanguageRepository')->findById(1);
        $this->language = $language->id;
        $this->systemRepository = $systemRepository;
    }

    public function getSystem() {
        $systems = convert_array(
            $this->systemRepository->findByCondition(
                [['language_id', '=', $this->language]], 
                true
            ), 
            'keyword', 
            'content'
        );

        return $systems;
    }
}
