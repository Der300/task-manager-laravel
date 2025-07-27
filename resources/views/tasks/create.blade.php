@extends('layouts.master')
@section('title', 'Task Create')
@section('content_wrapper')
    <div class="row">
        @include('tasks._form', [
            'action' => route('tasks.store'),
            'task' => $task,
            'statuses' => $statuses,
            'issueTypes' => $issueTypes,
            'createdUsers' => $createdUsers,
            'assignedUsers' => $assignedUsers,
            'projects' => $projects,
            'isCreate' => $isCreate,
        ])
    </div>
@endsection
