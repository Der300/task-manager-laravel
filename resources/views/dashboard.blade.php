@extends('layouts.master')
@section('title', 'Dashboard')
@section('content_wrapper')
    {{-- thong ke tong project, task, nhan vien, khach hang --}}
    <div class="row">
        @foreach ($boxes as $item)
            @if ($roleNotClient)
                <div class="col-12 col-sm-6 col-md-3">
                    <x-adminlte-small-box title="{{ $item['count'] }}" text="{{ $item['label'] }}" icon="{{ $item['icon'] }}"
                        theme="{{ $item['bg'] }}" url="{{ $item['link'] }}" url-text="More info" />
                </div>
            @else
                <div class="col-12 col-sm-6 col-md-3">
                    <x-adminlte-small-box title="{{ $item['count'] }}" text="{{ $item['label'] }}" icon="{{ $item['icon'] }}"
                        theme="{{ $item['bg'] }}" />
                </div>
            @endif
        @endforeach
    </div>

    <div class="row">
        @if ($roleNotClient)
            {{-- Thong ke project theo trang thai --}}
            <div class="col-md-6">
                @if (array_sum($projectSumary['data']) > 0)
                    <x-chart :chart="[
                        'id' => 'projectChart',
                        'icon' => 'fas fa-project-diagram',
                        'title' => 'Projects by Status',
                        'cardColor' => 'primary',
                        'height' => 200,
                        'type' => 'doughnut',
                        'labels' => $projectSumary['labels'],
                        'datasets' => [
                            [
                                'label' => 'amount',
                                'data' => $projectSumary['data'],
                                'backgroundColor' => $projectSumary['backgroundColor'],
                            ],
                        ],
                        'legendPosition' => 'left',
                        'legendDisplay' => true,
                        'scales' => [
                            'yAxes' => [
                                'gridLines' => [
                                    'drawOnChartArea' => false,
                                ],
                            ],
                        ],
                    ]" />
                @else
                    <div class="alert alert-info text-center">
                        No data available to display the Projects by Status chart.
                    </div>
                @endif
            </div>

            {{-- hien thi tien do project co chua cho tasks, tre dealine, ca tuan khong comment --}}
            @if ($roleAboveMember)
                <div class="col-md-6">
                    <div class="card card-danger card-outline" style="height: 288px">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                                Projects Needing Attention
                            </h3>
                        </div>
                        <div class="card-body" style="overflow: hidden; overflow-y: auto;">
                            <ul class="nav flex-column">
                                @forelse ($projectAttention as $project)
                                    <li class="nav-item border border-dark rounded shadow mb-1">
                                        <div class="nav-link d-flex flex-column">
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="far fa-circle text-primary"></i>
                                                    {{ Str::limit($project['project_name'], 50) }}
                                                </span>
                                                <a href="{{ route('projects.show', ['project' => $project['id']]) }}"
                                                    class="badge badge-danger badge-pill">View</a>
                                            </div>
                                            <div class="w-100 d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted">Manager: {{ $project['manager'] }}</small>
                                                <div>
                                                    @foreach ($project['issues'] ?? [] as $issue)
                                                        @if (isset($issue))
                                                            <span class="badge badge-{{ $issue['color'] }} mr-1">
                                                                {{ $issue['label'] }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="text-right">
                                                    <small class="text-muted">Due: {{ $project['due_date'] }}</small>
                                                </div>
                                            </div>
                                        </div> {{-- nav-link --}}
                                    </li>
                                @empty
                                    <li class="nav-item px-3 py-3 text-muted text-center">
                                        All projects look good. 🎉
                                    </li>
                                @endforelse

                            </ul>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
            @endif

            <!-- Thong ke task theo trang thai -->
            <div class="col-md-6">
                @if (array_sum($taskSumary['data']) > 0)
                    <x-chart :chart="[
                        'id' => 'taskChart',
                        'icon' => 'fas fa-tasks',
                        'title' => 'Tasks by Status',
                        'cardColor' => 'success',
                        'height' => 200,
                        'type' => 'pie',
                        'labels' => $taskSumary['labels'],
                        'datasets' => [
                            [
                                'label' => 'amount',
                                'data' => $taskSumary['data'],
                                'backgroundColor' => $taskSumary['backgroundColor'],
                            ],
                        ],
                        'legendPosition' => 'left',
                        'legendDisplay' => true,
                        'scales' => [
                            'yAxes' => [
                                'gridLines' => [
                                    'drawOnChartArea' => false,
                                ],
                            ],
                        ],
                    ]" />
                @else
                    <div class="alert alert-info text-center">
                        No data available to display the Tasks by Status chart.
                    </div>
                @endif
            </div>

            {{-- Thong ke so luong task cua moi nhan vien --}}
            @if ($roleAboveMember)
                <div class="col-md-6">
                    <x-chart :chart="[
                        'id' => 'taskUserChart',
                        'icon' => 'fas fa-users',
                        'title' => 'Task Workload by User',
                        'cardColor' => 'info',
                        'height' => $taskWorkload['heightCanvas'],
                        'responsive' => false,
                        'type' => 'horizontalBar',
                        'yAxisID' => 'y-axis-1',
                        'labels' => $taskWorkload['labels'],
                        'labelInfo' => $taskWorkload['labelInfo'],
                        'datasets' => [
                            [
                                'label' => 'Number of tasks',
                                'data' => $taskWorkload['data'],
                                'backgroundColor' => '#17a2b8',
                            ],
                        ],
                        'legendPosition' => 'bottom',
                        'legendDisplay' => false,
                        'scales' => [
                            'xAxes' => [
                                [
                                    'position' => 'top',
                                    'scaleLabel' => [
                                        'display' => true,
                                        'labelString' => 'Number of Tasks',
                                    ],
                                ],
                            ],
                            'yAxes' => [
                                [
                                    'id' => 'y-axis-1',
                                ],
                            ],
                        ],
                    ]" />
                </div>
            @endif

            {{-- Project Tracking Overview --}}
            <div class="col-md-12">
                @php
                    $chartData = [
                        'id' => 'projectProgressChart',
                        'labelInfo' => $tracking['labelInfo'],
                        'icon' => 'fas fa-chart-line',
                        'title' => 'Project Progress Overview - ' . $tracking['monthYear'],
                        'cardColor' => 'info',
                        'height' => 300,
                        'type' => 'bar',
                        'labels' => $tracking['labels'] ?? [],
                        'tooltips' => [
                            'enabled' => false,
                        ],
                        'datasets' => [
                            [
                                'type' => 'line',
                                'label' => 'Completed Tasks',
                                'data' => $tracking['completedTasks'] ?? [],
                                'backgroundColor' => 'rgba(40, 167, 69, 0.5)',
                                'borderColor' => 'rgba(40, 167, 69, 1)',
                                'yAxisID' => 'y-axis-tasks',
                            ],
                            [
                                'type' => 'bar',
                                'label' => 'Total Tasks',
                                'data' => $tracking['totalTasks'] ?? [],
                                'backgroundColor' => 'rgba(0, 123, 255, 0.3)',
                                'borderColor' => '#007bff',
                                'yAxisID' => 'y-axis-tasks',
                            ],
                            [
                                'type' => 'bar',
                                'label' => 'Worked days',
                                'data' => $tracking['workedDays'] ?? [],
                                'backgroundColor' => 'rgba(40, 167, 69, 0.5)',
                                'borderColor' => 'rgba(40, 167, 69, 1)',
                                'yAxisID' => 'y-axis-timeline',
                                'stack' => 'timeline',
                            ],
                            [
                                'type' => 'bar',
                                'label' => 'Remaining days',
                                'data' => $tracking['remainingDays'] ?? [],
                                'backgroundColor' => '#e6ad06',
                                'borderColor' => '#e6ad06',
                                'yAxisID' => 'y-axis-timeline',
                                'stack' => 'timeline',
                            ],
                        ],
                        'legendDisplay' => true,
                        'legendPosition' => 'top',
                        'scales' => [
                            'xAxes' => [
                                [
                                    'stacked' => true,
                                    'offset' => true,
                                ],
                            ],
                            'yAxes' => [
                                [
                                    'id' => 'y-axis-tasks',
                                    'type' => 'linear',
                                    'position' => 'left',
                                    'scaleLabel' => [
                                        'display' => true,
                                        'labelString' => 'Number of Tasks',
                                    ],
                                    'gridLines' => [
                                        'drawOnChartArea' => false,
                                    ],
                                ],
                                [
                                    'id' => 'y-axis-timeline',
                                    'type' => 'linear',
                                    'position' => 'right',
                                    'stacked' => true,
                                    'scaleLabel' => [
                                        'display' => true,
                                        'labelString' => 'Number of Days',
                                    ],
                                    'gridLines' => [
                                        'drawOnChartArea' => false,
                                    ],
                                ],
                            ],
                        ],
                    ];
                @endphp
                <x-chart :chart="$chartData" />
            </div>

            {{-- Thong ke tasks co thay doi trang thai trong tuan --}}
            @if ($roleAboveMember)
                <div class="col-md-6">
                    <div class="card card-outline card-success" style="height: 300px">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-pen text-success mr-2" aria-hidden="true"></i>
                                Active Task Status Updates This Week
                            </h3>
                        </div>
                        <div class="card-body" style="overflow:hidden; overflow-y:auto">
                            <ul class="list-group">
                                @forelse ($taskStatus as $item)
                                    <li class="list-group-item">
                                        <div>
                                            <span>{{ $item['task_name'] }}</span>
                                            <span class="badge float-right"
                                                style="background-color: {{ $item['status_color'] }}">{{ $item['status_name'] }}</span>
                                        </div>
                                        <div class="w-100 d-flex justify-content-between" style="font-size: 12px">
                                            <span class="text-muted">Project:
                                                {{ Str::limit($item['project_name'], 20) }}</span>
                                            <span class="text-muted">Assignee: {{ $item['assignee'] }}</span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">No activity in this week!</li>
                                @endforelse

                            </ul>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>

                {{-- Hien thi task qua han --}}
                <div class="col-md-6">
                    <div class="card card-danger card-outline" style="height: 300px">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-danger mr-2"></i> Overdue Tasks
                            </h3>
                        </div>
                        <div class="card-body" style="overflow:hidden; overflow-y:auto">
                            <ul class="nav flex-column border border-dark rounded">
                                @forelse ($taskOverdue as $item)
                                    <li class="nav-item">
                                        <div class="nav-link d-flex flex-column">
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="far fa-circle text-danger"></i>
                                                    {{ Str::limit($item['name'], 50) }}
                                                </span>
                                                <a href="{{ route('tasks.show', ['task' => $item['id']]) }}"
                                                    class="badge badge-danger badge-pill">View</a>
                                            </div>
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Assignee: {{ $item['assignee'] }}</small>
                                                <small class="text-muted">Due to: {{ $item['due_date'] }}</small>
                                                <small class="text-danger">Overdue by {{ $item['amount_day'] }}
                                                    days</small>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="nav-item">"🎉 All tasks are on track!"</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
            @endif

            {{-- Hien thi my tasks  --}}
            {{-- <div class="col-md-6">
                <div class="card card-outline card-success" style="height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-check-square text-success mr-2" aria-hidden="true"></i>
                            My Tasks
                        </h3>
                    </div>
                    <div class="card-body py-2" style="overflow:hidden; overflow-y: auto;">
                        <ul class="todo-list">
                            @forelse ($myTasks as $item)
                                <li class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="badge ml-2" style="background-color: {{ $item['status_color'] }}">
                                            {{ $item['status_name'] }}
                                        </span>
                                        <a href=""
                                            style="color: {{ $item['status_color'] }}">{{ $item['name'] }}</a>
                                    </div>
                                    <small class="badge badge-light" style="color: {{ $item['status_color'] }}"
                                        title="Updated at: {{ \Carbon\Carbon::parse($item['updated_at'])->format('d M Y, H:i') }}">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($item['updated_at'])->diffForHumans() }}
                                    </small>
                                </li>
                            @empty
                                <li class="text-muted">
                                    All clear! ✅ No tasks assigned.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer"></div>
                </div>

            </div> --}}

            {{-- Hien thi comments gan nhat --}}
            {{-- <div class="col-md-6">
                <div class="card card-outline card-warning" style="height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-comments text-warning mr-2"></i> Comments
                            latest
                        </h3>
                    </div>
                    <div class="card-body" style="overflow:hidden; overflow-y:auto">
                        @forelse ($comments as $item)
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="Email: {{ $item['user_email'] }}&#10;Position: {{ $item['user_position'] }}&#10;Department: {{ $item['user_department'] }}">
                                            {{ $item['user_name'] }}
                                        </span>
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="Project: {{ $item['project_name'] }}">
                                            <i class="fa fa-pen text-warning mx-1"></i>
                                            <a
                                                href="{{ route('tasks.show', ['task' => $item['task_id']]) }}">{{ Str::limit($item['task_name'], 30) }}</a>
                                        </span>
                                    </span>
                                    <span
                                        class="direct-chat-timestamp float-right">{{ Carbon\Carbon::parse($item['updated_at'])->format('d M g:i a') }}</span>
                                </div>
                                <img class="direct-chat-img" src="{{ asset('images/users/' . $item['user_image']) }}"
                                    alt="{{ $item['user_image'] }}">
                                <div class="direct-chat-text">
                                    {{ Str::limit($item['body'], 100) }}
                                </div>
                            </div>
                        @empty
                            <div class="direct-chat-msg text-muted">No recent messages 📭</div>
                        @endforelse
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div> --}}

            {{-- ----------------------------------------- phan hien thi cua client ------------------------------------------- --}}
        @else
            {{-- Hien thi thong tin project --}}
            <div class="col-md-6">
                <div class="card card-outline card-warning" style="height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-project-diagram text-warning mr-2"></i> Projects
                        </h3>
                    </div>
                    <div class="card-body" style="overflow: hidden; overflow-y: auto;">
                        <ul class="nav flex-column">
                            @foreach ($clientProjecs as $item)
                                <li class="nav-item border border-dark rounded shadow mb-1">
                                    <div class="nav-link d-flex flex-column">
                                        <div class="w-100 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="far fa-circle text-primary"></i>
                                                {{ Str::limit($item['name'], 50) }}
                                            </span>
                                            <a href="{{ route('projects.show', ['project' => $item['id']]) }}"
                                                class="badge badge-danger badge-pill">View</a>
                                        </div>
                                        <div class="w-100 d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-muted">Manager: {{ $item['manager'] }}</small>
                                            <div>
                                                <span class="badge mr-1"
                                                    style="background-color: {{ $item['status_color'] }}">
                                                    {{ $item['status_name'] }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-muted">Due: {{ $item['due_date'] }}</small>
                                            </div>
                                        </div>
                                    </div> {{-- nav-link --}}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>

            {{-- Hien thi tien do project  --}}
            <div class="col-md-6">
                <div class="card card-outline card-secondary" style="height: 300px;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-spinner mr-2 text-secondary" aria-hidden="true"></i>
                            Progress tasks
                        </h3>
                    </div>
                    <div class="card-body py-2" style="overflow:hidden; overflow-y: auto;">
                        <ul class="nav flex-column">
                            @foreach ($clientProgress as $item)
                                <li class="nav-item border border-dark rounded shadow mb-1">
                                    <div class="nav-link d-flex flex-column">
                                        <div class="w-100 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="far fa-circle text-{{ $item['theme'] }}"></i>
                                                {{ Str::limit($item['project_name'], 25) }}
                                            </span>
                                            <small class="text-muted">Manager: {{ $item['manager'] }}</small>
                                            <span class="badge mr-1"
                                                style="background-color: {{ $item['status_color'] }}">
                                                {{ $item['status_name'] }}
                                            </span>
                                        </div>
                                        @if ($item['valuePercent'] != 0)
                                            <x-adminlte-progress size='sm' theme="{{ $item['theme'] }}"
                                                value="{{ $item['valuePercent'] }}" animated with-label />
                                        @else
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-secondary" style="width: 100%">
                                                    <span class="text-l">{{ $item['valuePercent'] }}%</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer"></div>
                </div>

            </div>

        @endif

        {{-- Hien thi task list  --}}
        <div class="col-md-6">
            <div class="card card-outline card-success" style="height: 300px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-tasks text-success mr-2" aria-hidden="true"></i>
                        My Task
                    </h3>
                </div>
                <div class="card-body py-2" style="overflow:hidden; overflow-y: auto;">
                    <ul class="nav flex-column">
                        @forelse ($myTasks as $item)
                            <li class="nav-item border border-dark rounded shadow mb-1">
                                <div class="nav-link d-flex flex-column">
                                    <div class="w-100 d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="far fa-circle text-primary"></i>
                                            {{ Str::limit($item['name'], 50) }}
                                        </span>
                                        <a href="{{ route('tasks.show', ['task' => $item['id']]) }}"
                                            class="badge badge-danger badge-pill">View</a>
                                    </div>
                                    <div class="w-100 d-flex justify-content-between align-items-center mt-1">
                                        <div>
                                            <span class="badge mr-1"
                                                style="background-color: {{ $item['status_color'] }}">
                                                {{ $item['status_name'] }}
                                            </span>
                                        </div>
                                        <small class="text-muted">Project:
                                            {{ Str::limit($item['project_name'], 25) }}</small>
                                        <small class="text-muted">Assignee: {{ $item['assignee'] }}</small>
                                    </div>
                                </div>
                            </li>
                        @empty
                        <li class="nav-item border border-dark rounded shadow mb-1">You have no tasks at the moment. 🕒</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>

        {{-- Hien thi comment  --}}
        <div class="col-md-6">
            <div class="card card-outline card-warning" style="height: 300px;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-comments text-warning mr-2"></i> Comments
                        latest
                    </h3>
                </div>
                <div class="card-body" style="overflow:hidden; overflow-y:auto">
                    @forelse ($comments as $item)
                        <div class="direct-chat-msg">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-left">
                                    <span data-toggle="tooltip" data-placement="top"
                                        title="Email: {{ $item['user_email'] }}&#10;Position: {{ $item['user_position'] }}&#10;Department: {{ $item['user_department'] }}">
                                        {{ $item['user_name'] }}
                                    </span>
                                    <span data-toggle="tooltip" data-placement="top"
                                        title="Project: {{ $item['project_name'] }}">
                                        <i class="fa fa-pen text-warning mx-2"></i>
                                        <a
                                            href="{{ route('tasks.show', ['task' => $item['task_id']]) }}">{{ Str::limit($item['task_name'], 30) }}</a>
                                    </span>

                                </span>
                                <span
                                    class="direct-chat-timestamp float-right">{{ Carbon\Carbon::parse($item['updated_at'])->format('d M g:i a') }}</span>
                            </div>
                            <img class="direct-chat-img" src="{{ asset('images/users/' . $item['user_image']) }}"
                                alt="{{ $item['user_image'] }}">
                            <div class="direct-chat-text">
                                {{ Str::limit($item['body'], 100) }}
                            </div>
                        </div>
                    @empty
                        <div class="direct-chat-msg text-muted">No recent messages 📭</div>
                    @endforelse
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
@endsection
