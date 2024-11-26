<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // Nó sự tự động lấy $user hiện tại đang đăng nhập và gửi vào
        Gate::define('modules', function($user, $permissionName) {
            if ($user->publish == 1) return false;
            $permission = $user->user_catalogues->permission;
            if ($permission->contains('canonical', $permissionName)) {
                return true;
            } 
            return false;
        });
    }
}
