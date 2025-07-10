{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

@extends('layouts.auth')

@section('auth_title', 'Confirm Password')
@section('auth_color', 'warning')
@section('auth_header_message', 'This is a secure area of the application. Please confirm your password before continuing.')

@section('auth_content')
    <form action="{{ route('password.confirm') }}" method="POST">
        @csrf
        {{-- password --}}
        <div class="form-group auth-wrapper">
            <input type="password" name="password" id="password" class="form-control input" autocomplete="TRUE"
                placeholder="" required>
            <label for="password" class="label">Password</label>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fa fa-eye-slash icon" aria-hidden="true" id="eye-icon-password"></i>
            </span>
        </div>

        {{-- action --}}
        <div class="w-100 d-flex mb-3 justify-content-center">
            <button type="submit" class="btn btn-warning w-50">Confirm</button>
        </div>

        {{-- hien thi status/error --}}
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @error('password')
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @enderror
    </form>
@endsection

