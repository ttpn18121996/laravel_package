<?php

use Illuminate\Support\Facades\Route;

Route::prefix('dashboard_admin_23644466')->group(function() {
    Route::get('login', 'Auth\LoginController@index')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('change-password', 'Auth\ChangePasswordController@showChangePasswordPage')->name('change-password');
        Route::post('change-password', 'Auth\ChangePasswordController@changePassword');

    Route::middleware(['auth:phuongnam', 'check_locale', 'check_login_first_time'])->group(function () {
        Route::get('/', 'DashboardController')->name('home');

        // dashboard_admin_23644466/user, dashboard_admin_23644466/user/{id}
        Route::group([
            'prefix' => 'user',
            'as' => 'userandpermission.user.'
        ], function () {
            Route::get('/', 'UserController@index')->name('index')->middleware('check_user_permissions:view_user');
            Route::get('create', 'UserController@create')
                ->name('create')
                ->middleware('check_user_permissions:create_user');
            Route::post('/', 'UserController@store')
                ->name('store')
                ->middleware('check_user_permissions:create_user');
            Route::get('{id}', 'UserController@show')->name('show')->middleware('check_user_permissions:view_user');
            Route::get('{id}/edit', 'UserController@edit')->name('edit')->middleware('check_user_permissions:edit_user');
            Route::put('{id}', 'UserController@update')->name('update')->middleware('check_user_permissions:edit_user');
            Route::patch('{id}', 'UserController@updateStatus')
                ->name('updateStatus')
                ->middleware('check_user_permissions:edit_user');
            Route::patch('{id}/restore', 'UserController@restore')->name('restore');
            Route::delete('{id}', 'UserController@destroy')
                ->name('destroy')
                ->middleware('check_user_permissions:delete_user');
            Route::delete('{id}/delete', 'UserController@forceDelele')->name('forceDelete');

            Route::resource('profile', 'UserProfileController')->only(['index', 'store']);
            Route::resource('settings', 'UserSettingsController')->only(['index', 'store']);
        });

        // dashboard_admin_23644466/group, dashboard_admin_23644466/group/{id}
        Route::group([
            'prefix' => 'group',
            'as' => 'userandpermission.group.'
        ], function () {
            Route::get('/', 'GroupController@index')->name('index')->middleware('check_user_permissions:view_group');
            Route::get('create', 'GroupController@create')->name('create')->middleware('check_user_permissions:create_group');
            Route::post('/', 'GroupController@store')->name('store')->middleware('check_user_permissions:create_group');
            Route::get('{id}', 'GroupController@show')->name('show')->middleware('check_user_permissions:view_group');
            Route::get('{id}/edit', 'GroupController@edit')->name('edit')->middleware('check_user_permissions:edit_group');
            Route::put('{id}', 'GroupController@update')->name('update')->middleware('check_user_permissions:edit_group');
            Route::delete('{id}', 'GroupController@destroy')->name('destroy')->middleware('check_user_permissions:delete_group');
        });

        // dashboard_admin_23644466/history, dashboard_admin_23644466/history/{id}
        Route::resource('history', 'HistoryController', [
            'as' => 'userandpermission',
            'only' => ['index', 'show']
        ]);
    });
});
Route::get('api/unauthorized', 'Auth\ApiLoginController@unauthorized')->name('unauthorized');
