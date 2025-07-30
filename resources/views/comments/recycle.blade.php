@extends('layouts.master')
@section('title', 'Comment Recycle')
@section('content_wrapper')

    <div class="card shadow-sm">
        @forelse ($data as $item)
            <div class="card shadow-sm mb-2 position-relative">
                <div class="card-body d-flex align-items-start">
                    <div>
                        <img src="{{ asset('images/users/' . $item->user?->image) }}" class="rounded-circle mr-2 bg-secondary"
                            width="50" height="50" alt="{{ $item->user?->name }}">
                    </div>
                    <div>
                        {{-- User Info --}}
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $item->user?->name }}</h6>
                                <small class="text-muted mt-0 d-block">
                                    @if ($item->user?->role !== 'client')
                                        --Client--
                                    @else
                                        {{ $item->user?->position ?? '' }} -
                                        {{ $item->user?->department ?? '' }}
                                    @endif
                                </small>
                                <small class="text-muted mt-0 d-block">
                                    {{ $item->updated_at?->format('d/m/Y H:i:s') ?? '--' }}
                                </small>
                            </div>
                            <div>
                                <i class="fa fa-paper-plane text-warning mx-2" aria-hidden="true"></i>
                                <a href="{{ route('tasks.show', ['task' => $item->task_id, 'comment_id' => $item->id]) }}">
                                    {{ $item->task?->name }}
                                </a>
                            </div>
                        </div>
                        {{-- Comment Body --}}
                        <p class="m-0"><i class="fa fa-reply text-warning mr-2" style="transform: rotateZ(180deg)"
                                aria-hidden="true"></i>{{ $item->body }}</p>
                    </div>
                </div>
                <div class="position-absolute d-flex" style="bottom: 0; right:0">
                    @can('comment.force-delete')
                        <form action="{{ route('comments.force-delete', ['comment' => $item->id]) }}" method="POST" class="mx-1"
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
                    <form action="{{ route('comments.restore', ['comment' => $item->id]) }}" method="POST" class="mx-1"
                        onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Restore',text: 'Are you sure you want to restore?',confirmButtonText: 'restore'})">
                        @csrf
                        <span data-toggle="tooltip" data-placement="top" title="Restore">
                            <button class="btn btn-success btn-sm px-2 py-1">
                                <i class="fa fa-window-restore" aria-hidden="true"></i>
                            </button>
                        </span>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No comments found.</div>
            </div>
        @endforelse

        <div class="card-footer mb-0 pb-0">
            {{ $data->links() }}
        </div>
    </div>
@endsection
