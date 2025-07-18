<aside class="main-sidebar sidebar-dark-primary elevation-4">
    {{-- Brand Logo --}}
    <a href="{{ route('dashboard') }}" class="brand-link p-0 text-center" style="height: 48px; overflow:hidden;">
        <img src="{{ asset('images/brand_without_bg.png') }}" alt="brand logo"
            style="height:44px; object-fit:cover; box-shadow: 0 4px 10px rgba(255, 255, 255, 0.5);">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        {{-- user panel --}}
        <div class="border-top border-bottom my-2">
            {{-- user info --}}
            <div class="user-panel d-flex align-items-center py-2" data-toggle="collapse"
                data-target="#sidebar-user-dropdown" aria-expanded="false" aria-controls="sidebar-user-dropdown"
                style="cursor: pointer;" id="sidebar-user-toggle">
                <div class="image">
                    <img src="{{ asset('images/users/avatar_' . str_replace('-', '', \Illuminate\Support\Str::slug(auth()->user()->name)) . '.svg') }}"
                        class="img-circle elevation-2" alt="{{ auth()->user()->name }}">

                </div>
                <div class="info d-flex flex-column align-items-start justify-content-center overflow-hidden">
                    <span class="d-block" style="line-height:1.2; font-size: 16px">{{ auth()->user()->name }}</span>
                    <span class="d-block text-muted"
                        style="line-height:1.2; font-size: 8px">{{ auth()->user()->email }}</span>
                </div>
            </div>

            {{-- Dropdown menu of  User Panel: Profile, Logout --}}
            <div id="sidebar-user-dropdown" class="collapse px-3 py-2">
                <a class="dropdown-item" href="">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault();this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </form>
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-home" aria-hidden="true"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                {{-- Manage user --}}
                <li class="nav-item {{ request()->routeIs('users.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-user" aria-hidden="true"></i>
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        <p>
                            Manage user
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="" class="nav-link">
                                <i class="fa fa-user-plus nav-icon ml-3" aria-hidden="true"></i>
                                <p>New User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                <i class="fa fa-list nav-icon ml-3" aria-hidden="true"></i>
                                <p>User List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="pages/widgets.html" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Widgets
                            <span class="right badge badge-danger">New</span>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
