<!DOCTYPE html>
<html>

<head>
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
</head>

@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <x-preloader /> {{-- Preloader --}}

        @include('partials.main_navbar') {{-- navbar --}}

        @include('partials.main_sidebar') {{-- sidebar --}}

        {{-- content wrapper --}}
        <div class="content-wrapper" style="margin-top: 48px; padding-bottom:0">
            {{-- page name vs breadcum --}}
            @include('partials.breadcrumb')
            {{-- Main content --}}
            <section class="content">
                <div class="container-fluid">
                    @yield('content_wrapper')
                </div>
            </section>
        </div>
    </div>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    @include('partials.main_footer')
    </div><!-- ./wrapper -->

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    @stack('js')
    @include('components.sweet-alert')
    {{-- @yield('script-confirm') --}}
    {{-- search --}}
    <script type="text/javascript">
        const searchInput = document.getElementById('global-search');
        const resultBox = document.getElementById('search-results');
        let delayTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(delayTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                resultBox.style.display = 'none';
                return;
            }

            delayTimer = setTimeout(() => {
                fetch(`/search/json?search=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        let html = '';

                        if (data.users.length) {
                            data.users.forEach(user => {
                                html +=
                                    `<a href="/users/${user.id}" class="dropdown-item"><i class="fas fa-user mr-1"></i>${user.name}</a>`;
                            });
                        }

                        if (data.projects.length) {
                            data.projects.forEach(project => {
                                html +=
                                    `<a href="/projects/${project.id}" class="dropdown-item"><i class="fas fa-project-diagram mr-1"></i>${project.name}</a>`;
                            });
                        }

                        if (data.tasks.length) {
                            data.tasks.forEach(task => {
                                html +=
                                    `<a href="/tasks/${task.id}" class="dropdown-item"><i class="fas fa-tasks mr-1"></i>${task.name}</a>`;
                            });
                        }

                        resultBox.innerHTML = html ||
                            '<span class="dropdown-item text-muted">No results</span>';
                        resultBox.style.display = 'block';
                    });
            }, 300); // Delay 300ms tránh spam server
        });

        // Ẩn dropdown khi click ngoài
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) {
                resultBox.style.display = 'none';
            }
        });

    </script>

</body>

</html>
