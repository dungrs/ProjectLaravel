<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\LanguageRepository;
use App\Models\Language;

class LanguageComposerServiceProvider extends ServiceProvider
{   
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Đăng ký các bindings cần thiết
        $this->app->bind(
            'App\Repositories\Interfaces\LanguageRepositoryInterface',
            'App\Repositories\LanguageRepository'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Dùng Dependency Injection để lấy đối tượng của LanguageRepository
        view()->composer('backend.dashboard.layout', function ($view) {
            // Sử dụng Dependency Injection để lấy đối tượng LanguageRepository
            $languageRepository = app("App\Repositories\LanguageRepository");
            $languages = $languageRepository->all();
            $view->with('languages', $languages);
            
        });
    }
}
