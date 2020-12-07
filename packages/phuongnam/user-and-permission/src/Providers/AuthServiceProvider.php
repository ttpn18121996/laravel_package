<?php

namespace PhuongNam\UserAndPermission\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'PhuongNam\UserAndPermission\Models\User' => 'PhuongNam\UserAndPermission\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('is_admin', function ($user) {
            return $user->is_admin;
        });
        Gate::define('check_user_permission', function ($user, $permission) {
            if ($user->is_admin) {
                return true;
            }

            $permissions = session()->get('user_permissions', function () { return []; });
            if (is_array($permission)) {
                $checkPermission = array_intersect($permission, $permissions);
                return (count($checkPermission) > 0);
            }

            return in_array($permission, $permissions);
        });

        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(1));
        Passport::refreshTokensExpireIn(now()->addDays(15));
    }
}
