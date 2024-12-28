<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\LanguageRepository;

class LanguageComposer {

    protected $languageRepository;
    protected $language;

    public function __construct(LanguageRepository $languageRepository, $language) {
        $this->languageRepository = $languageRepository;
        $this->language = $language;
    }

    public function compose(View $view) {
        $languages = $this->languageRepository->findByCondition(
            [
                config('apps.general.defaultPublish')
            ],
            true,
            [],
            ['current' => 'desc'],
            ['language.*']
        );
        $view->with('languages', $languages);
    }
}
