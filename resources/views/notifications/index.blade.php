@extends('layouts.master')
@section('title', 'Notifications')
@section('content_wrapper')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            @forelse ($notifications ?? [] as $notification)
                <div
                    class="card mb-3 shadow-sm @if ($isUser) {{ $notification->read_at ? '' : 'bg-secondary' }} @endif">
                    <div class="card-body pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                @if ($notification->data['type'] === 'task')
                                    <i class="fas fa-tasks mr-2 text-secondary"></i>
                                @elseif($notification->data['type'] === 'comment')
                                    <i class="fas fa-comments mr-2 text-secondary"></i>
                                @elseif($notification->data['type'] === 'project')
                                    <i class="fas fa-project-diagram mr-2 text-secondary"></i>
                                @elseif($notification->data['type'] === 'file')
                                    <i class="fa fa-file mr-2 text-secondary" aria-hidden="true"></i>
                                @endif
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h5>
                            @if ($isUser)
                                @if ($notification->read_at)
                                    <span class="badge badge-secondary">Seen</span>
                                @else
                                    <span class="badge badge-primary">New</span>
                                @endif
                            @endif
                        </div>
                        <div style="margin-left: 32px">
                            <small><i class="fa fa-home text-warning mr-2"
                                    aria-hidden="true"></i>{{ $notification->data['created_by'] ?? '' }}</small>
                            <small><i class="fa fa-forward text-primary mx-2"
                                    aria-hidden="true"></i>{{ $isUser ? 'You' : $notification->data['assigned_to'] }}</small>
                        </div>
                        <div class="my-2 w-100 rounded shadow" style="margin-left: 32px">
                            <span>{{ $notification->data['object_name'] ?? '' }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if ($isUser)
                            <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        @endif
                        <span class="card-text text-muted float-right">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    You don’t have any notifications.
                </div>
            @endforelse
            <div class="col-md-12">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection
