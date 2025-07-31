@extends('layouts.master')
@section('title', 'Project Recycle')
@section('content_wrapper')

    <div class="card">
        <div class="card-body">
            @if ($data->count())
                <table class="table table-hover table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" style="width: 10px">#</th>
                            <th class="align-middle">Name</th>
                            <th class="align-middle">Description</th>
                            <th class="align-middle">Status</th>
                            <th class="align-middle">Assigned to</th>
                            <th class="align-middle">Client</th>
                            <th class="align-middle">Due day</th>
                            <th class="align-middle">Deleted at</th>
                            <th class="align-middle">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($data ?? [] as $item)
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
                                <td class="align-middle" style="background-color: {{ $item->status?->color }}">
                                    {{ $item->status?->name }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->assignedUser->name }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->clientUser?->name }}
                                </td>
                                <td class="align-middle">
                                    {{ \Carbon\Carbon::parse($item->due_date)?->format('d/m/Y') ?? '--' }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->deleted_at?->format('d/m/Y H:i:s') ?? '--' }}
                                </td>
                                <td class="align-middle text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        @can('project.force-delete')
                                            <form action="{{ route('projects.force-delete', $item) }}" method="POST"
                                                class="mx-1"
                                                onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Delete',text: 'Are you sure you want to delete?',confirmButtonText: 'delete'})">
                                                @csrf
                                                @method('DELETE')
                                                <span data-toggle="tooltip" data-placement="top" title="Delete">
                                                    <button class="btn btn-danger btn-sm px-2 py-1">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </span>
                                            </form>
                                        @endcan
                                        <form action="{{ route('projects.restore', $item) }}" method="POST" class="mx-1"
                                            onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Restore',text: 'Are you sure you want to restore?',confirmButtonText: 'restore'})">
                                            @csrf
                                            <span data-toggle="tooltip" data-placement="top" title="Restore">
                                                <button class="btn btn-success btn-sm px-2 py-1">
                                                    <i class="fa fa-window-restore" aria-hidden="true"></i>
                                                </button>
                                            </span>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-info text-center">Recycle is empty!</div>
            @endif
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
            {{ $data?->links() }}
        </div>
    </div>
    <!-- /.card -->
@endsection
