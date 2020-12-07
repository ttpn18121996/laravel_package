<?php

namespace PhuongNam\UserAndPermission\View\Components;

use Illuminate\View\Component;

class HeaderPage extends Component
{

    /**
     * @var string
     */
    public $currentPage;

    /**
     * @var array
     */
    public $parentPage;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($currentPage, $parentPage = [])
    {
        $this->currentPage = $currentPage;
        $this->parentPage = $parentPage;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.header-page');
    }
}
