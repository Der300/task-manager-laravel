@extends('layouts.auth')

@section('auth_title', 'Login')
@section('auth_color', 'primary')
@section('auth_content')
    <form action="{{ route('login') }}" method="POST">
        @csrf
        {{-- email --}}
        <div class="form-group auth-wrapper">
            <input type="text" name="email" id="email" class="form-control input" autocomplete="TRUE" placeholder=""
                autofocus required>
            <label for="email" class="label">Email</label>
        </div>

        {{-- password --}}
        <div class="form-group auth-wrapper">
            <input type="password" name="password" id="password" class="form-control input" autocomplete="TRUE"
                placeholder="" required>
            <label for="password" class="label">Password</label>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fa fa-eye-slash icon" aria-hidden="true" id="eye-icon-password"></i>
            </span>
        </div>

        {{-- remember --}}
        <div class="d-flex align-items-center">
            <input name="remember" id="remember" class="mr-2" type="checkbox"
                style="margin-top: 3px; font-size:20px; transform: scale(1.5);">
            <label for="remember" class="m-0 text-white"> Remember me</label>
        </div>

        {{-- action --}}
        <div class="d-flex align-items-center auth-wrapper justify-content-center flex-wrap">
            <button type="submit" class="btn btn-primary w-50">
                Login
            </button>
            <a href="{{ route('password.request') }}" class="w-50 text-center text-light">Forgot your password?</a>
        </div>

        {{-- hien thi error --}}
        @if ($errors->has('email') || $errors->has('password'))
            <div class="alert alert-danger">
                {{ $errors->first('email') ?: $errors->first('password') }}
            </div>
        @endif
    </form>
@endsection
