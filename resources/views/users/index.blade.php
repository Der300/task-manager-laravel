@extends('layouts.master')
@section('title', 'Users')
@section('content_wrapper')

    <div class="card">
        @if ($roleAboveManager)
            <div class="card-header">
                <a href="{{ route('users.create') }}" class="btn btn-success">
                    <i class="fa fa-plus mr-2" aria-hidden="true"></i> Create User
                </a>
            </div>
        @endif
        <div class="card-body">
            <table class="table table-hover table-bordered">
                <thead class="text-center">
                    <tr>
                        <th class="align-middle" style="width: 10px">#</th>
                        <th class="align-middle">Image</th>
                        <th class="align-middle">Name</th>
                        <th class="align-middle">Email</th>
                        <th class="align-middle">Position</th>
                        <th class="align-middle">Department</th>
                        @if ($roleAboveManager)
                            <th class="align-middle" style="width: 40px">Status</th>
                            <th class="align-middle">Created at</th>
                            <th class="align-middle">Updated at</th>
                        @endif
                        @if ($roleAboveMember)
                            <th class="align-middle">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($data as $item)
                        <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle">
                                <img src="{{ asset("images/users/$item->image") }}" alt="{{ $item->name }}"
                                    style="width:40px">
                            </td>
                            <td class="align-middle">{{ $item->name }}</td>
                            <td class="align-middle">{{ $item->email }}</td>
                            <td class="align-middle">{{ $item->position ?? 'empty' }}</td>
                            <td class="align-middle">{{ $item->department ?? 'empty' }}</td>
                            @if ($roleAboveManager)
                                <td class="align-middle"><span
                                        class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ $item->status }}</span>
                                </td>
                                <td style="font-size: 12px" class="align-middle">
                                    {{ $item->created_at?->format('d/m/Y H:i:s') ?? '--' }}
                                </td>
                                <td style="font-size: 12px" class="align-middle">
                                    {{ $item->created_at?->format('d/m/Y H:i:s') ?? '--' }}
                                </td>
                            @endif
                            @if ($canManageUser($item))
                                @if ($roleAboveMember)
                                    <td class="align-middle text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            @if ($roleAboveManager)
                                                <form action="{{ route('users.soft-delete', ['user' => $item->id]) }}"
                                                    method="POST" class="mx-1"
                                                    onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?'})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <span data-toggle="tooltip" data-placement="top"
                                                        title="Move to Recycle">
                                                        <button class="btn btn-danger btn-sm px-2 py-1">
                                                            <i class="fa fa-recycle" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </form>
                                                <a href="{{ route('users.show', ['user' => $item->id]) }}"
                                                    class="btn btn-warning btn-sm px-2 py-1 mx-1" data-toggle="tooltip"
                                                    data-placement="top" title="Detail">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            @if ($canResetPassword($item))
                                                <form action="{{ route('users.send-reset-link', ['user' => $item->id]) }}"
                                                    method="POST" class="mx-1">
                                                    @csrf
                                                    <span data-toggle="tooltip" data-placement="top" title="Reset Password">
                                                        <button class="btn btn-info btn-sm px-2 py-1">
                                                            <i class="fa fa-key" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
            {{ $data->links() }}
        </div>
    </div>

@endsection
