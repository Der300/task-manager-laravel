{{-- <x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

@extends('layouts.auth')

@section('auth_title', 'Reset password')
@section('auth_color', 'info')
@section('auth_content')
    <form action="{{ route('password.store') }}" method="POST">
        @csrf
        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{$request->route('token')}}">
        {{-- email --}}
        <div class="form-group auth-wrapper">
            <input type="text" name="email" id="email" class="form-control input" value="{{old('email', $request->email)}}" autocomplete="TRUE" placeholder="">
            <label for="email" class="label">Email</label>
        </div>

        {{-- new password --}}
        <div class="form-group auth-wrapper">
            <input type="password" name="password" id="password" class="form-control input" autocomplete="TRUE"
                placeholder="" required autofocus>
            <label for="password" class="label">New Password</label>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fa fa-eye-slash icon" aria-hidden="true" id="eye-icon-password"></i>
            </span>
        </div>

        {{-- confirm password --}}
        <div class="form-group auth-wrapper">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control input" autocomplete="TRUE"
                placeholder="" required>
            <label for="password_confirmation" class="label">Password</label>
            <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                <i class="fa fa-eye-slash icon" aria-hidden="true" id="eye-icon-password_confirmation"></i>
            </span>
        </div>

        {{-- action --}}
        <div class="d-flex align-items-center auth-wrapper justify-content-center flex-wrap">
            <button type="submit" class="btn btn-info w-50">
                Reset pasword
            </button>
        </div>

        {{-- hien thi error --}}
        @error('password_confirmation')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </form>
@endsection
