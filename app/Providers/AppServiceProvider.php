<?php

namespace App\Providers;

use App\Http\ViewComposers\SystemComposer;
use App\Http\ViewComposers\MenuComposer;
use App\Http\ViewComposers\LanguageComposer;

use App\Repositories\LanguageRepository;
use App\Repositories\SystemRepository;
use App\Repositories\MenuCatalogueRepository;

use App\Models\Language;
use DateTime;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{   
    public $serviceBindings = [
        'App\Services\Interfaces\UserServiceInterface' => 'App\Services\UserService',
        'App\Services\Interfaces\UserCatalogueServiceInterface' => 'App\Services\UserCatalogueService',
        'App\Services\Interfaces\CustomerServiceInterface' => 'App\Services\CustomerService',
        'App\Services\Interfaces\CustomerCatalogueServiceInterface' => 'App\Services\CustomerCatalogueService',
        'App\Services\Interfaces\LanguageServiceInterface' => 'App\Services\LanguageService',
        'App\Services\Interfaces\PostCatlogueServiceInterface' => 'App\Services\PostCatalogueService',
        'App\Services\Interfaces\PostServiceInterface' => 'App\Services\PostService',
        'App\Services\Interfaces\GenerateServiceInterface' => 'App\Services\GenerateService',
        'App\Services\Interfaces\PermissionServiceInterface' => 'App\Services\PermissionService',
        'App\Services\Interfaces\ProductCatalogueServiceInterface' => 'App\Services\ProductCatalogueService',
        'App\Services\Interfaces\ProductServiceInterface' => 'App\Services\ProductService',
        'App\Services\Interfaces\AttributeCatalogueServiceInterface' => 'App\Services\AttributeCatalogueService',
        'App\Services\Interfaces\AttributeServiceInterface' => 'App\Services\AttributeService',
        'App\Services\Interfaces\SystemServiceInterface' => 'App\Services\SystemService',
        'App\Services\Interfaces\MenuServiceInterface' => 'App\Services\MenuService',
        'App\Services\Interfaces\MenuCatalogueServiceInterface' => 'App\Services\MenuCatalogueService',
        'App\Services\Interfaces\SlideServiceInterface' => 'App\Services\SlideService',
        'App\Services\Interfaces\WidgetServiceInterface' => 'App\Services\WidgetService',
        'App\Services\Interfaces\PromotionServiceInterface' => 'App\Services\PromotionService',
        'App\Services\Interfaces\SourceServiceInterface' => 'App\Services\SourceService',
        'App\Services\Interfaces\OrderServiceInterface' => 'App\Services\OrderService',
    ];  

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {   
        foreach ($this->serviceBindings as $key => $value) {
            $this->app->bind($key, $value);
        }

        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {   
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();

        // Mở rộng phương thức validate cho định dạng ngày tháng
        Validator::extend('custom_date_format', function($attribute, $value, $parameters, $validator) {
            // Kiểm tra định dạng ngày với giờ
            return DateTime::createFromFormat('d/m/Y H:i', $value) !== false;
        });

        // Mở rộng phương thức validate cho kiểm tra ngày bắt đầu và kết thúc
        Validator::extend('custom_after', function($attribute, $value, $parameters, $validator) {
            // Lấy giá trị start_date từ request
            $startDate = $validator->getData()['start_date'] ?? null;
        
            // Kiểm tra nếu start_date và end_date hợp lệ và so sánh
            return $startDate && DateTime::createFromFormat('d/m/Y H:i', $value) > DateTime::createFromFormat('d/m/Y H:i', $startDate);
        });

        $composers = [
            SystemComposer::class => SystemRepository::class,
            MenuComposer::class => MenuCatalogueRepository::class,
            LanguageComposer::class => LanguageRepository::class,
        ];
        
        foreach ($composers as $composerClass => $repositoryClass) {
            View::composer('frontend.homepage.layout', function ($view) use ($composerClass, $repositoryClass, $language) {
                $repository = app($repositoryClass);
                $languageId = $language->id ?? app('App\\Repositories\\LanguageRepository')->findById(1)->id;
                $composer = new $composerClass($repository, $languageId);
                $composer->compose($view);
            });
        }

        // Đặt chiều dài mặc định cho chuỗi trong schema
        Schema::defaultStringLength(250);
    }
}
