<?php

namespace PhuongNam\UserAndPermission\Providers;

use Illuminate\Support\ServiceProvider;

class UserAndPermissionServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerViews();
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->singleton(
            \PhuongNam\UserAndPermission\Repositories\User\User::class,
            \PhuongNam\UserAndPermission\Repositories\User\UserRepository::class
        );
        $this->app->singleton(
            \PhuongNam\UserAndPermission\Repositories\Group\Group::class,
            \PhuongNam\UserAndPermission\Repositories\Group\GroupRepository::class
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/user-and-permission');
        $sourcePath = __DIR__.'/../resources/views';

        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, 'userandpermission');
        } else {
            $this->loadViewsFrom($sourcePath, 'userandpermission');
        }
    }

    public function registerObservers()
    {
        User::observe(UserObserver::class);
        Group::observe(GroupObserver::class);
    }
}
