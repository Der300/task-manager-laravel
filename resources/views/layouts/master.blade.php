<!DOCTYPE html>
<html>

<head>
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> --}}
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

            @yield('content_wrapper')

        </div>
    </div>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    @include('partials.main_footer')
    </div><!-- ./wrapper -->

    {{-- JS --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
</body>

</html>
