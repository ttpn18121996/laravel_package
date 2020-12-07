<nav class="main-header navbar navbar-expand {{ $colorThemeClass ?? 'navbar-primary' }}">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a href="#" class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user mr-2"></i>{{ human_name(auth()->user()->name)->first_name }}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ auth()->user()->email }}</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('userandpermission.user.profile.index') }}" class="dropdown-item">
                    <i class="fas fa-users-cog mr-2"></i> @lang('Profile')
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('change-password') }}" class="dropdown-item">
                    <i class="fas fa-exchange-alt mr-2"></i> @lang('Change password')
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('userandpermission.history.index') }}" class="dropdown-item">
                    <i class="fas fa-history mr-2"></i> @lang('History')
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('userandpermission.user.settings.index') }}" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> @lang('Setting')
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer">
                    <i class="fas fa-sign-out-alt mr-2"></i>@lang('Logout')
                </a>
            </div>
        </li>
    </ul>
</nav>
