<aside class="main-sidebar sidebar-dark-primary elevation-4">
    {{-- Brand Logo --}}
    <a href="{{ route('dashboard') }}" class="brand-link p-0 text-center" style="height: 48px; overflow:hidden;">
        <img src="{{ asset('images/brand_without_bg.png') }}" alt="brand logo"
            style="height:44px; object-fit:cover; box-shadow: 0 4px 10px rgba(255, 255, 255, 0.5);">
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="transition: width 0.3s ease, margin 0.3s ease;">
        {{-- user panel --}}
        <div class="border-top border-bottom my-2">
            {{-- user info --}}
            <div class="user-panel d-flex align-items-center py-2" data-toggle="collapse"
                data-target="#sidebar-user-dropdown" aria-expanded="false" aria-controls="sidebar-user-dropdown"
                style="cursor: pointer;" id="sidebar-user-toggle">
                <div class="image">
                    <img src="{{ asset('images/users/'.auth()->user()->image) }}"
                        class="img-circle elevation-2" alt="{{ auth()->user()->name }}">

                </div>
                <div class="info d-flex flex-column align-items-start justify-content-center overflow-hidden">
                    <span class="d-block" style="line-height:1.2; font-size: 16px">{{ auth()->user()->name }}</span>
                    <span class="d-block text-muted"
                        style="line-height:1.2; font-size: 8px">{{ auth()->user()->email }}</span>
                </div>
            </div>

            {{-- Dropdown menu of  User Panel: Profile, Logout --}}
            <div id="sidebar-user-dropdown" class="collapse px-3 py-2"
                style="transition: height 0.4s ease, opacity 0.3s ease;">
                <a class="dropdown-item" href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <a class="dropdown-item text-danger" href="#"
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
                @if ($roleNotClient)
                    <li class="nav-item {{ request()->routeIs('users.*') ? 'menu-open' : '' }}">
                        <div class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-user" aria-hidden="true"></i>
                            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                            <p>
                                Manage users
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </div>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                    <i class="fa fa-list nav-icon ml-3" aria-hidden="true"></i>
                                    <p>User List</p>
                                </a>
                            </li>
                            @if ($roleAdminOrSuper)
                                <li class="nav-item">
                                    <a href="{{ route('users.create') }}"
                                        class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}">
                                        <i class="fa fa-user-plus nav-icon ml-3" aria-hidden="true"></i>
                                        <p>New User</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('users.recycle') }}"
                                        class="nav-link {{ request()->routeIs('users.recycle') ? 'active' : '' }}">
                                        <i class="fa fa-trash nav-icon" style="margin-left:12px" aria-hidden="true"></i>
                                        <p>User recycle</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Manage project --}}
                <li class="nav-item {{ request()->routeIs('projects.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>
                            Manage projects
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('projects.index') }}"
                                class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}">
                                <i class="fa fa-list nav-icon ml-3" aria-hidden="true"></i>
                                <p>Project List</p>
                            </a>
                        </li>
                        @if ($roleAboveLeader)
                            <li class="nav-item">
                                <a href="{{ route('projects.create') }}" class="nav-link">
                                    <i class="fa fa-plus nav-icon ml-3"></i>
                                    <p>New Project</p>
                                </a>
                            </li>
                        @endif
                        @if ($roleAboveLeader)
                            <li class="nav-item">
                                <a href="{{ route('projects.recycle') }}"
                                    class="nav-link {{ request()->routeIs('projects.recycle') ? 'active' : '' }}">
                                    <i class="fa fa-trash nav-icon ml-3" aria-hidden="true"></i>
                                    <p>Project recycle</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                {{-- Manage task --}}
                <li class="nav-item {{ request()->routeIs('tasks.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks nav-icon"></i>
                        <p>
                            Manage tasks
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}"
                                class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                                <i class="fa fa-list nav-icon ml-3" aria-hidden="true"></i>
                                <p>Task List</p>
                            </a>
                        </li>
                        @if ($roleAboveMember)
                            <li class="nav-item">
                                <a href="{{ route('tasks.create') }}" class="nav-link">
                                    <i class="fa fa-plus nav-icon ml-3"></i>
                                    <p>New Task</p>
                                </a>
                            </li>
                        @endif
                        @if ($roleAboveLeader)
                            <li class="nav-item">
                                <a href="{{ route('tasks.recycle') }}"
                                    class="nav-link {{ request()->routeIs('tasks.recycle') ? 'active' : '' }}">
                                    <i class="fa fa-trash nav-icon ml-3" aria-hidden="true"></i>
                                    <p>Task recycle</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                {{--Manage comment  --}}
                <li class="nav-item {{ request()->routeIs('comments.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('comments.*') ? 'active' : '' }}">
                        <i class="fa fa-comment nav-icon" aria-hidden="true"></i>
                        <p>
                            Manage comments
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('comments.index') }}"
                                class="nav-link {{ request()->routeIs('comments.index') ? 'active' : '' }}">
                                <i class="fa fa-list nav-icon ml-3" aria-hidden="true"></i>
                                <p>Comment List</p>
                            </a>
                        </li>
                        @if ($roleAboveLeader)
                            <li class="nav-item">
                                <a href="{{ route('comments.recycle') }}"
                                    class="nav-link {{ request()->routeIs('comments.recycle') ? 'active' : '' }}">
                                    <i class="fa fa-trash nav-icon ml-3" aria-hidden="true"></i>
                                    <p>Comment recycle</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('notifications.index') }}"
                        class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-bell" aria-hidden="true"></i>
                        <p>Notifications</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
