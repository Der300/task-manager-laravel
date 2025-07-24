@extends('layouts.auth-without-header')

@section('auth_title', 'Verification Email Status')
@section('auth_color', 'success')

@section('auth_content')
    <div class="card">
        <div class="card-body {{ $status === 'success' ? 'text-success' : 'text-danger' }}">
            <i class="fa {{ $status === 'success' ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
            <span>
                {{ $status === 'success' ? 'Your email has been successfully verified!' : 'Email verification failed. Please try again.' }}
            </span>
        </div>
    </div>
@endsection
