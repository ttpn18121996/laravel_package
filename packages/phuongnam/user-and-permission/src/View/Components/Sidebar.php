<?php

namespace PhuongNam\UserAndPermission\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Gate;

class Sidebar extends Component
{
    /**
     * Hiển thị menu.
     *
     * @return string
     */
    public function renderMenu()
    {
        $menuItems = [
            [
                'text' => __('User and permission'),
                'icon' => 'fas fa-users',
                'route' => [
                    'view_user' => 'userandpermission.user.index',
                    'view_group' => 'userandpermission.group.index',
                ],
                'items' => [
                    [
                        'text' => __('Users list'),
                        'icon' => 'fas fa-user',
                        'route' => ['view_user' => 'userandpermission.user.index']
                    ],
                    [
                        'text' => __('Groups list'),
                        'icon' => 'fas fa-user-friends',
                        'route' => ['view_group' => 'userandpermission.group.index'],
                    ]
                ]
            ],
            [
                'text' => __('Histories list'),
                'icon' => 'fas fa-history',
                'route' => ['view_history' => 'userandpermission.history.index']
            ]
        ];

        return $this->getMenuItems($menuItems);
    }

    /**
     * Lấy các template của menu items.
     *
     * @param  array  $menuItems
     * @return string
     */
    private function getMenuItems(array $menuItems = [])
    {
        $result = '';
        if (empty($menuItems)) {
            return $result;
        }

        foreach ($menuItems as $menuItem) {
            if (isset($menuItem['items'])) {
                $permissions = array_keys($menuItem['route']);
                if (Gate::check('check_user_permission', [$permissions])) {
                    $result .= '<li class="nav-item has-treeview '.$this->activeMenu($menuItem['route'], 'menu-open').'">
                        <a href="#" class="nav-link '.$this->activeMenu($menuItem['route']).'">
                            <i class="nav-icon '.$menuItem['icon'].'"></i>
                            <p>'.$menuItem['text'].'<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">'.$this->getMenuItems($menuItem['items']).'</ul>
                    </li>';
                }
            } else {
                $permission = array_keys($menuItem['route']);
                $route = array_values($menuItem['route'])[0];
                if (Gate::check('check_user_permission', [$permission])) {
                    $result .= '<li class="nav-item">
                        <a href="'.route($route).'" class="nav-link '.$this->activeMenu($route).'">
                            <i class="nav-icon '.$menuItem['icon'].'"></i>
                            <p>'.$menuItem['text'].'</p>
                        </a>
                    </li>';
                }
            }
        }

        return $result;
    }

    /**
     * Kích hoạt menu.
     *
     * @param  array|string  $route
     * @param  string  $className
     * @return string
     */
    public function activeMenu($route, $className = 'active')
    {
        $currentRoute = Route::currentRouteName();
        if (is_array($route)) {
            foreach ($route as $value) {
                if ($value == $currentRoute) {
                    return $className;
                }
            }
        } else {
            if ($route == $currentRoute) {
                return $className;
            }
        }
    }

    /**
     * Lấy class cho element của giao diện.
     *
     * @return string
     */
    public function getColorThemeClass()
    {
        $setting = auth()->user()->setting;

        if (! is_null($setting)) {
            if ($setting->color == 'light' || $setting->color == 'dark') {
                return 'sidebar-'.$setting->theme.'-primay';
            } else {
                return 'sidebar-'.$setting->theme.'-'.$setting->color;
            }
        }

        return 'sidebar-dark-primary';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.sidebar', [
            'colorThemeClass' => $this->getColorThemeClass(),
        ]);
    }
}
