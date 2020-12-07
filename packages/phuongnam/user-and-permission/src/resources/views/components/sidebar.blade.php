<aside class="main-sidebar elevation-4 text-sm {{ $colorThemeClass ?? 'sidebar-dark-primary' }}">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('static/imgs/user-default.png') }}" class="img-circle elevation-2 bg-white" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-legacy nav-flat nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>@lang('Home')</p>
                    </a>
                </li>
                <li class="nav-header text-uppercase">@lang('System')</li>
                {!! $renderMenu() !!}
            </ul>
        </nav>
    </div>
</aside>
