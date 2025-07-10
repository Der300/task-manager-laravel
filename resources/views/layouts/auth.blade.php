<!DOCTYPE html>
<html>

<head>
    <title>@yield('auth_title')</title>
    <link rel="stylesheet" href="{{ asset('adminlteV3_2_0/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlteV3_2_0/plugins/fontawesome-free/css/all.min.css') }}">
    {{-- them style cho header card, label animation cua input --}}
    <style>
        .auth-page {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background: url('{{ asset('images/background.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }

        .card-custom {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        .auth-wrapper {
            position: relative;
            margin: 20px 0;
        }

        .label {
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);

            padding: 0 5px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            background: white;
        }

        .auth-wrapper:focus-within .label,
        .auth-wrapper .input:not(:placeholder-shown)+.label,
        .auth-wrapper .select:valid+.label {
            top: 0;
            font-size: 12px;
        }

        .icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .text-s24 {
            font-size: 24px;
        }

        .text-shadow {
            text-shadow: 4px 4px 4px rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body>
    {{-- header --}}
    <div class="w-100 position-fixed bg-white text-center top-0 start-0 d-flex align-items-center justify-content-between"
        style="height: 48px">
        {{-- brand logo, message --}}
        <div class="text-@yield('auth_color') h-100">
            <a href={{ route('login') }}>
                <img src="{{ asset('images/brand_without_bg.png') }}" alt="brand logo"
                    style="height:100%; object-fit:cover; border-right: solid 1px rgb(221, 221, 221); margin-right:4px">
            </a>
            @yield('auth_header_message')
        </div>
        {{-- login button --}}
        <div class="mr-3">
            <a class="btn btn-outline-secondary" href="{{ route('login') }}">Login</a>
        </div>
    </div>

    {{-- body --}}
    <div class="auth-page">
        <div class="card card-@yield('auth_color') card-outline w-25 card-custom">
            {{-- title --}}
            <div class="card-header text-center font-weight-bold text-shadow text-s24 p-2 text-uppercase text-white">
                @yield('auth_title')
            </div>
            {{-- content --}}
            <div class="card-body py-0">
                @yield('auth_content')
            </div>
        </div>
    </div>

    {{-- chuyen doi giua type password va text de xem --}}
    <script type="text/javascript">
        function togglePassword(inputId = 'password') {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(`eye-icon-${inputId}`);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.add('fa-eye');
                icon.classList.remove('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.add('fa-eye-slash');
                icon.classList.remove('fa-eye');
            }
        }
    </script>
</body>

</html>
