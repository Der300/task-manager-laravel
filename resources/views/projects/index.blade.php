@extends('layouts.master')
@section('title', 'Projects')
@section('content_wrapper')
    <div class="card">
        <div class="card-header pb-0">
            <form method="GET" action="{{ route('projects.index') }}" class="row">
                <div class="col-md-2 mb-2">
                    @if ($roleAboveLeader)
                        <a href="{{ route('users.create') }}" class="btn btn-success">
                            <i class="fa fa-plus mr-2" aria-hidden="true"></i> Create Projects
                        </a>
                    @endif
                </div>
                {{-- Tìm kiếm tên project --}}
                <div class="col-md-2 mb-2">
                    <input type="text" name="search_project" class="form-control"
                        placeholder="Search project name or description" value="{{ request('search_project') }}">
                </div>

                {{-- Tìm kiếm client/assigned to --}}
                <div class="col-md-2 mb-2">
                    <input type="text" name="search_user" class="form-control"
                        placeholder="Search manager name or client name" value="{{ request('search_user') }}">
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">-- Status --</option>
                        @foreach ($statuses ?? [] as $value => $name)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Time --}}
                <div class="col-md-2 mb-2">
                    <select name="time" class="form-control">
                        <option value="">-- Select Time --</option>
                        <option value="due_date_soon" {{ request('time') == 'due_date_soon' ? 'selected' : '' }}>Due Date
                            Soonest</option>
                        <option value="updated_at_new" {{ request('time') == 'updated_at_new' ? 'selected' : '' }}>Most
                            Recently Updated</option>
                    </select>
                </div>

                {{-- Nút lọc & reset --}}
                <div class="col-md-2 mb-2 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
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
                            <th class="align-middle">Name</th>
                            <th class="align-middle">Description</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">Assignee</th>
                            @if ($exceptClient)
                                <th class="align-middle">Client</th>
                                <th class="align-middle">Due day</th>
                            @endif
                            <th class="align-middle">Updated at</th>
                            <th class="align-middle">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($data as $item)
                            <tr>
                                <td class="align-middle">
                                    {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->name }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->description }}
                                </td>
                                <td class="align-middle" style="background-color: {{ $item->status->color }}">
                                    {{ $item->status->name }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->assignedUser->name }}
                                </td>
                                @if ($exceptClient)
                                    <td class="align-middle">
                                        {{ $item->clientUser->name }}
                                    </td>
                                    <td class="align-middle">
                                        {{ \Carbon\Carbon::parse($item->due_date)?->format('d/m/Y') ?? '--' }}
                                    </td>
                                @endif
                                <td class="align-middle">
                                    {{ $item->updated_at?->format('d/m/Y H:i:s') ?? '--' }}
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center justify-content-center">
                                        @if ($canSoftDel($item))
                                            <form action="{{ route('projects.soft-delete', $item) }}" method="POST"
                                                class="mx-1"
                                                onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?'})">
                                                @csrf
                                                @method('DELETE')
                                                <span data-toggle="tooltip" data-placement="top" title="Move to Recycle">
                                                    <button class="btn btn-danger btn-sm px-2 py-1">
                                                        <i class="fa fa-recycle" aria-hidden="true"></i>
                                                    </button>
                                                </span>
                                            </form>
                                        @endif
                                        <a href="{{ route('projects.show', $item) }}"
                                            class="btn btn-warning btn-sm px-2 py-1 mx-1" data-toggle="tooltip"
                                            data-placement="top" title="Detail">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
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
