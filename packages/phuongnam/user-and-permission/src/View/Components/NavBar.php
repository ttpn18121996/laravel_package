<?php

namespace PhuongNam\UserAndPermission\View\Components;

use Illuminate\View\Component;

class NavBar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
            switch ($setting->color) {
                case 'light':
                    return 'navbar-light navbar-white';
                case 'warning':
                    return 'navbar-light navbar-warning';
                default:
                    return 'navbar-dark navbar-'.$setting->color;
            };
        }

        return 'navbar-light navbar-white';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.nav-bar', [
            'colorThemeClass' => $this->getColorThemeClass(),
        ]);
    }
}
