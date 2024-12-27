<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\SystemRepository;

class SystemComposer {

    protected $systemRepository;
    protected $language;

    public function __construct(SystemRepository $systemRepository, $language) {
        $this->systemRepository = $systemRepository;
        $this->language = $language;
    }

    public function compose(View $view) {
        $systems = convert_array(
            $this->systemRepository->findByCondition(
                [['language_id', '=', $this->language]], 
                true
            ), 
            'keyword', 
            'content'
        );
        $view->with('systems', $systems);
    }
}
