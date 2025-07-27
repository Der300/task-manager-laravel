@extends('layouts.master')
@section('title', 'Project Detail')
@section('content_wrapper')
    <div class="row">
        @include('projects._form', [
            'action' => route('projects.update', $project),
            'project' => $project,
            'statuses' => $statuses,
            'issueTypes' => $issueTypes,
            'createdUsers' => $createdUsers,
            'assignedUsers' => $assignedUsers,
            'clients' => $clients,
            'isCreate' => $isCreate,
        ])
        <div class="col-md-12 col-sm-12">
            <div class="card card-success" style="height: 400px;">
                <div class="card-header">
                    <h3 class="card-title">Tasks of Project</h3>
                </div>
                <div class="card-body py-2" style="overflow:hidden; overflow-y: auto;">
                    <ul class="list-unstyled">
                        @forelse ($projectTasks as $item)
                            <li class="nav-item border border-dark rounded shadow mb-2 p-2">
                                <div class="w-100 row">
                                    <span class="col-md-4 col-sm-12">
                                        <i class="far fa-circle text-primary"></i>
                                        {{ Str::limit($item->name, 50) }}
                                    </span>
                                    <div class="col-md-4 col-sm-6 text-center">
                                        <span class="badge mr-1" style="background-color: {{ $item->status?->color }}">
                                            {{ $item->status?->name }}
                                        </span>
                                    </div>
                                    <div class="col-md-4 col-sm-6 text-right">
                                        <a href="{{ route('tasks.show', ['task' => $item->id]) }}"
                                            class="badge badge-primary badge-pill">View</a>
                                    </div>
                                </div>
                                <div class="w-100 row mt-1">
                                    <small class="text-muted col-md-4 col-sm-4">Assignee:
                                        {{ $item->assignedUser?->name }}</small>
                                    <small class="text-muted col-md-4 col-sm-4 text-center">Due:
                                        {{ \Carbon\Carbon::parse($item->due_date)?->format('d/m/Y') ?? '--' }}</small>
                                    <small class="text-muted col-md-4 col-sm-4 text-right">Updated:
                                        {{ $item->updated_at?->format('d/m/Y H:i:s') ?? '--' }}</small>
                                </div>
                            </li>
                        @empty
                            <li class="nav-item border border-dark rounded shadow mb-1">No tasks for this project. 🕒
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
@endsection


