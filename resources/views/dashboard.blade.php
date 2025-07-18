@extends('layouts.master')
@section('title', 'Dashboard')
@section('content_wrapper')
    {{-- Main content --}}
    <section class="content">
        <div class="container-fluid">
            {{-- thong ke tong project, task, nhan vien, khach hang --}}
            <div class="row">
                @foreach ($boxes as $item)
                    <div class="col-12 col-sm-6 col-md-3">
                        <x-adminlte-small-box title="{{ $item['count'] }}" text="{{ $item['label'] }}"
                            icon="{{ $item['icon'] }}" theme="{{ $item['bg'] }}" url="{{ $item['link'] }}"
                            url-text="More info" />
                    </div>
                @endforeach
            </div>

            {{-- Thong ke project theo trang thai & hien thi tien do project có deadline gan nhat --}}
            <div class="row">
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
                <div class="col-md-6">
                    <div class="card card-danger card-outline" style="height: 288px">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                                Projects Needing Attention
                            </h3>
                        </div>
                        <div class="card-body py-2" style="overflow: hidden; overflow-y: auto;">
                            <ul class="nav flex-column">
                                @forelse ($projectAttention as $project)
                                    <li class="nav-item border border-dark rounded shadow mb-1">
                                        <div class="nav-link d-flex flex-column">
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="far fa-circle text-primary"></i>
                                                    {{ Str::limit($project['project_name'], 50) }}
                                                </span>
                                                <a href="" class="badge badge-danger badge-pill">View</a>
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
            </div>
            {{-- Project Tracking Overview --}}
            <div class="row">
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
            </div>

            {{-- Thong ke task theo trang thai & hien thi 5 nguoi co task nhieu nhat ma chua hoan thanh  --}}
            <div class="row">
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
                <div class="col-md-6">
                    <x-chart :chart="[
                        'id' => 'taskUserChart',
                        'icon' => 'fas fa-users',
                        'title' => 'Task Workload by User',
                        'cardColor' => 'info',
                        'height' => $taskWordload['heightCanvas'],
                        'responsive' => false,
                        'type' => 'horizontalBar',
                        'yAxisID' => 'y-axis-1',
                        'labels' => $taskWordload['labels'],
                        'datasets' => [
                            [
                                'label' => 'Number of tasks',
                                'data' => $taskWordload['data'],
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
            </div>

            {{-- Thong ke tasks co thay doi trang thai trong tuan & Hien thi task qua han --}}
            <div class="row">
                <div class="col-md-6"> {{-- Thong ke tasks co thay doi trang thai trong tuan --}}
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
                <div class="col-md-6"> {{-- Hien thi task qua han --}}
                    <div class="card card-danger card-outline" style="height: 300px">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-danger mr-2"></i> Overdue Tasks
                            </h3>
                            <div class="card-tools">
                                <a href="" class="btn btn-tool" title="View all">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
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
                                                <a href="" class="badge badge-danger badge-pill">View</a>
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
            </div>

            {{-- Hien thi comments va my tasks --}}
            <div class="row">
                {{-- Hien thi comments gan nhat --}}
                <div class="col-md-6">
                    <div class="card card-outline card-warning" style="height: 300px;">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-comments text-warning mr-2"></i> Comments
                                latest
                            </h3>
                        </div>
                        <div class="card-body py-0">
                            <div class="direct-chat-messages">
                                @forelse ($comments as $item)
                                    <div class="direct-chat-msg">
                                        <div class="direct-chat-infos clearfix">
                                            <span class="direct-chat-name float-left">
                                                <span data-toggle="tooltip" data-placement="top"
                                                    title="Email: {{ $item['user_email'] }}&#10;Position: {{ $item['user_position'] }}&#10;Department: {{ $item['user_department'] }}">
                                                    {{ $item['user_name'] }}
                                                </span>
                                                <i class="fa fa-pen text-warning mx-2"></i>
                                                <a
                                                    href="">{{ Str::limit($item['task_name'], 30) }}</a>
                                            </span>
                                            <span
                                                class="direct-chat-timestamp float-right">{{ Carbon\Carbon::parse($item['updated_at'])->format('d M g:i a') }}</span>
                                        </div>
                                        <img class="direct-chat-img"
                                            src="{{ asset('images/users/' . $item['user_image']) }}"
                                            alt="{{ $item['user_image'] }}">
                                        <div class="direct-chat-text">
                                            {{ Str::limit($item['body'], 100) }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="direct-chat-msg text-muted">No recent messages 📭</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
                {{-- Hien thi my tasks  --}}
                <div class="col-md-6">
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
                                            <span class="badge ml-2"
                                                style="background-color: {{ $item['status_color'] }}">
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

                </div>
            </div>
        </div>
    </section>
@endsection
