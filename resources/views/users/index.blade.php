@extends('layouts.master')
@section('title', 'Users')
@section('content_wrapper')

    <div class="card">
        <div class="card-header pb-0">
            <form method="GET" action="{{ route('users.index') }}" class="row">
                <div class="col-md-2 mb-2">
                    @if ($roleAboveManager)
                        <a href="{{ route('users.create') }}" class="btn btn-success">
                            <i class="fa fa-plus mr-2" aria-hidden="true"></i> Create User
                        </a>
                    @endif
                </div>
                {{-- Tìm kiếm tên/email --}}
                <div class="col-md-2 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Search name or email"
                        value="{{ request('search') }}">
                </div>

                {{-- Phòng ban --}}
                <div class="col-md-2 mb-2">
                    <select name="department" class="form-control">
                        <option value="">-- Department --</option>
                        @foreach ($departments ?? [] as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Chức vụ --}}
                <div class="col-md-2 mb-2">
                    <select name="position" class="form-control">
                        <option value="">-- Position --</option>
                        @foreach ($positions ?? [] as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                                {{ $pos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">-- Status --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                {{-- Nút lọc & reset --}}
                <div class="col-md-2 mb-2 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fa fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
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
                            <th class="align-middle">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($data as $item)
                            <tr>
                                <td class="align-middle">
                                    {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
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
                                <td class="align-middle text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        @if ($canManageUser($item))
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
                                            @endif
                                            @if ($canResetPassword($item))
                                                <form action="{{ route('users.send-reset-link', ['user' => $item->id]) }}"
                                                    method="POST" class="mx-1">
                                                    @csrf
                                                    <span data-toggle="tooltip" data-placement="top" title="Reset Password">
                                                        <button type="submit" class="btn btn-info btn-sm px-2 py-1">
                                                            <i class="fa fa-key" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </form>
                                            @endif
                                        @endif
                                        @if ($canSeeProfile($item))
                                            <a href="{{ route('users.show', ['user' => $item->id]) }}"
                                                class="btn btn-warning btn-sm px-2 py-1 mx-1" data-toggle="tooltip"
                                                data-placement="top" title="Detail">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
            {{ $data->links() }}
        </div>
    </div>

@endsection
