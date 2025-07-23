<nav class="main-header navbar navbar-expand navbar-dark" style="height: 48px">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline" onsubmit="return false;">
                    <div class="input-group input-group-sm position-relative">
                        <input id="global-search" class="form-control form-control-navbar" name="search" type="search"
                            placeholder="Search user, project, task" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search"><i
                                    class="fas fa-times"></i></button>
                        </div>

                        <!-- Kết quả hiển thị -->
                        <div id="search-results" class="dropdown-menu show w-100"
                            style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 9999;">
                        </div>
                    </div>
                </form>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if ($notifications?->count())
                    <span class="badge badge-warning navbar-badge">{{ $notifications->count() ?? 0 }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ $notifications?->count() ?? 0 }} Unread
                    Notifications</span>
                @foreach ($notifications ?? [] as $notification)
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item">
                        @if ($notification->data['type'] === 'assigned')
                            <i class="fas fa-tasks mr-2"></i>
                        @elseif($notification->data['type'] === 'comment')
                            <i class="fas fa-comments mr-2"></i>
                        @endif

                        {{ $notification->data['title'] ?? 'Notification' }}
                        <span class="float-right text-muted text-sm">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </a>
                @endforeach
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
    </ul>
</nav>
