@extends('layouts.master')
@section('title', 'Users')
@section('content_wrapper')

    <div class="card">
        <div class="card-body">
            @if ($data->count())
                <table class="table table-hover table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" style="width: 10px">#</th>
                            <th class="align-middle">Image</th>
                            <th class="align-middle">Name</th>
                            <th class="align-middle">Email</th>
                            <th class="align-middle">Position</th>
                            <th class="align-middle">Department</th>
                            <th class="align-middle" style="width: 40px">Status</th>
                            <th class="align-middle">Deleted at</th>
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
                                <td class="align-middle"><span
                                        class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-danger' }}">{{ $item->status }}</span>
                                </td>
                                <td style="font-size: 12px" class="align-middle">
                                    {{ $item->created_at?->format('d/m/Y H:i:s') ?? '--' }}
                                </td>
                                <td style="font-size: 12px" class="align-middle">
                                    {{ $item->deleted_at?->format('d/m/Y H:i:s') ?? '--' }}
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
            {{ $data->links() }}
        </div>
    </div>
    <!-- /.card -->


@endsection
