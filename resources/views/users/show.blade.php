@extends('layouts.master')
@section('title', 'Profile')
@section('content_wrapper')
    <div class="row">
        @include('users._form', [
            'action' => route('users.update', ['user' => $user->id]),
            'user' => $user,
            'positions' => $positions,
            'departments' => $departments,
            'roles' => $roles,
            'isCreate' => $isCreate,
        ])
        @if (Auth::user()->id === $user->id)
            <form method="POST" action="{{ route('password.update') }}" class="col-md-12 col-sm-12">
                @csrf
                @method('PUT')
                <div class="card card-info w-100">
                    <div class="card-header">Update Password</div>
                    <div class="card-body d-flex flex-column align-items-center">
                        <div class="form-group w-50">
                            <label for="current_password" class="form-label">Current Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required
                                autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mật khẩu mới --}}
                        <div class="form-group w-50">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Xác nhận mật khẩu --}}
                        <div class="form-group w-50">
                            <label for="password_confirmation" class="form-label">Confirm Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-info">Save change</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
