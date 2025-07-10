@extends('layouts.auth')

@section('auth_title', 'Forgot password')
@section('auth_color', 'warning')
@section('auth_header_message', 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.')

@section('auth_content')
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        {{-- email --}}
        <div class="form-group auth-wrapper">
            <input type="text" name="email" id="email" value="{{old('email')}}" class="form-control input" autocomplete="TRUE" placeholder=""
                required autofocus>
            <label for="email" class="label">Email for reset password</label>
        </div>

        {{-- action --}}
        <div class="w-100 d-flex mb-3 justify-content-center">
            <button type="submit" class="btn btn-warning w-50">Send Reset Link</button>
        </div>

        {{-- hien thi status/error --}}
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @error('email')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </form>
@endsection


