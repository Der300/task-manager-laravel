@extends('layouts.master')
@section('title', 'Create User')
@section('content_wrapper')
    @include('users._form', [
        'action' => route('users.store'),
        'user' => $user,
        'positions' => $positions,
        'departments' => $departments,
        'roles' => $roles,
        'isCreate' => $isCreate,
    ])
@endsection