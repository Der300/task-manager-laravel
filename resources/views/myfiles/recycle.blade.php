@extends('layouts.master')
@section('title', 'Files Recycle')
@section('content_wrapper')
    <div class="card card-primary">
        <div class="card-header"></div>
        <div class="card-body">
            @if (count($files))
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="align-middle">File</th>
                                <th class="align-middle">Type</th>
                                <th class="align-middle">Descrition</th>
                                <th class="align-middle">Task</th>
                                <th class="align-middle">Uploader</th>
                                <th class="align-middle">Upload at</th>
                                <th class="align-middle">Updated at</th>
                                <th class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($files as $item)
                                <tr id="file-{{ $item->id }}">
                                    <td class="align-middle">{{ $item->original_name }}</td>
                                    <td class="align-middle">
                                        {{ app(App\Services\File\FileService::class)->getFileTypeLabel($item->mime_type) }}
                                    </td>
                                    <td class="align-middle">{{ $item->description }}</td>
                                    <td class="align-middle">
                                        <a
                                            href="{{ route('tasks.show', ['task' => $item->task?->id]) }}">{{ $item->task?->name }}</a>
                                    </td>
                                    <td class="align-middle">{{ $item->uploader?->name }}</td>
                                    <td class="align-middle">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="align-middle">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center justify-content-center">
                                            @can('file.force-delete')
                                            {{-- @can gọi từ permission của user --}}
                                                <form action="{{ route('myfiles.force-delete', ['file' => $item->id]) }}"
                                                    method="POST" class="mx-1"
                                                    onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?'})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <span data-toggle="tooltip" data-placement="top" title="Delete">
                                                        <button class="btn btn-danger btn-sm px-2 py-1">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </form>
                                            @endcan
                                            <form action="{{ route('myfiles.restore', ['file' => $item->id]) }}"
                                                method="POST" class="mx-1"
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
                </div>
            @else
                <p class="text-muted mb-0">No files attached yet.</p>
            @endif
        </div>

        <div class="card-footer"></div>
    </div>
    </div>
@endsection
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // highlight file
            const urlParams = new URLSearchParams(window.location.search);
            const fileId = urlParams.get('file_id');

            if (fileId) {
                const target = document.getElementById(`file-${fileId}`);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Highlight tạm thời
                    target.classList.add('highlight-comment');
                    setTimeout(() => {
                        target.classList.remove('highlight-comment');
                    }, 2000);
                }
            }
        })
    </script>
@endpush
