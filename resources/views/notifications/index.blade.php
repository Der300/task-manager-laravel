@extends('layouts.master')
@section('title', 'Notifications')
@section('content_wrapper')
    @php
        $notifications = collect([
            (object) [
                'id' => 1,
                'data' => [
                    'title' => 'A new task has been assigned to you',
                    'url' => '/tasks/1',
                    'task_name' => 'Design Landing Page',
                    'type' => 'assigned',
                ],
                'read_at' => null,
                'created_at' => Carbon\Carbon::now()->subMinutes(10),
            ],
            (object) [
                'id' => 2,
                'data' => [
                    'title' => 'Someone commented on your task',
                    'url' => '/tasks/2',
                    'task_name' => 'Fix login bug',
                    'type' => 'comment',
                ],
                'read_at' => Carbon\Carbon::now()->subMinutes(5),
                'created_at' => Carbon\Carbon::now()->subHours(1),
            ],
        ]);
    @endphp
    <div class="row">
        <div class="col-md-12 col-sm-12">
            @forelse ($notifications ?? [] as $notification)
                <div class="card mb-3 shadow-sm {{$notification->read_at? '' : 'bg-secondary'}}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h5>
                            @if ($notification->read_at)
                                <span class="badge badge-secondary">Seen</span>
                            @else
                                <span class="badge badge-primary">New</span>
                            @endif
                        </div>
                        <p class="card-text mt-2 text-muted">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                        <a href="{{ route('notifications.read', $notification->id) }}"
                            class="btn btn-sm btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    You don’t have any notifications.
                </div>
            @endforelse
        </div>
    </div>
@endsection
