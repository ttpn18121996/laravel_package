<?php

namespace PhuongNam\UserAndPermission\Providers;

use Illuminate\Support\ServiceProvider;

class UserAndPermissionServiceProvider extends ServiceProvider
{
    protected $moduleName = 'user-and-permission';

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
        $viewPath = resource_path('views/phuongnam/' . $this->moduleName);

        $sourcePath = __DIR__.'/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleName . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'phuongnam_userandpermission');
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/phuongnam/' . $this->moduleName)) {
                $paths[] = $path . '/phuongnam/' . $this->moduleName;
            }
        }
        return $paths;
    }

    public function registerObservers()
    {
        User::observe(UserObserver::class);
        Group::observe(GroupObserver::class);
    }
}
