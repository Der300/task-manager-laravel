<!DOCTYPE html>
<html>

<head>
    <title>@yield('auth_title')</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('components.sweet-alert')
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
    @yield('auth-script')
</body>

</html>
