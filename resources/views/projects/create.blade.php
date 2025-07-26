@extends('layouts.master')
@section('title', 'Project Detail')
@section('content_wrapper')
    <div class="row">
        @include('projects._form', [
            'action' => route('projects.store'),
            'project' => $project,
            'statuses' => $statuses,
            'issueTypes' => $issueTypes,
            'createdUsers' => $createdUsers,
            'assignedUsers' => $assignedUsers,
            'clients' => $clients,
            'isCreate' => $isCreate,
        ])
    </div>
@endsection