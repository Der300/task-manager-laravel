@extends('layouts.master')

@section('title', 'Project List')
@section('content_wrapper')
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach ($users as $user) --}}
                <tr>
                    <td>{{ '$user->id' }}</td>
                    <td>{{ '$user->name' }}</td>
                    <td>{{ '$user->email' }}</td>
                </tr>
            {{-- @endforeach --}}
        </tbody>
    </table>
    </section>
@endsection
