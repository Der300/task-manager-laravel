@extends('layouts.master')
@section('title', 'Task Detail')
@section('content_wrapper')
    <div class="row">
        @include('tasks._form', [
            'action' => route('tasks.update', ['task' => $task->id]),
            'task' => $task,
            'statuses' => $statuses,
            'issueTypes' => $issueTypes,
            'createdUsers' => $createdUsers,
            'assignedUsers' => $assignedUsers,
            'projects' => $projects,
            'isCreate' => $isCreate,
        ])
        {{-- Files --}}
        @php
            $files = [
                (object) [
                    'original_name' => 'report_2025_q2.pdf',
                    'mime_type' => 'application/pdf',
                    'uploaded_by' => 'Nguyễn Văn A',
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(2),
                ],
                (object) [
                    'original_name' => 'design_sketch.png',
                    'mime_type' => 'image/png',
                    'uploaded_by' => 'Trần Thị B',
                    'created_at' => now()->subDays(8),
                    'updated_at' => now()->subDays(3),
                ],
                (object) [
                    'original_name' => 'task_list.xlsx',
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'uploaded_by' => 'Lê Văn C',
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(1),
                ],
            ];
        @endphp
        <div class="col-md-12 col-sm-12">
            <div class="card mb-3 card-primary">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#fileCollapse"
                    aria-expanded="true">
                    <h3 class="card-title">Attachment files</h3>
                </div>

                <div id="fileCollapse" class="collapse">
                    <div class="card-body" style="height: 300px; overflow-y:auto">
                        @if (count($files))
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>File</th>
                                            <th>Type</th>
                                            <th>Descrition</th>
                                            <th>Uploader</th>
                                            <th>Upload at</th>
                                            <th>Updated at</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($files as $file)
                                            <tr>
                                                <td>{{ $file->original_name }}</td>
                                                <td>{{ $file->mime_type }}</td>
                                                <td>{{' $file->description' }}</td>
                                                <td>{{ $file->uploaded_by }}</td>
                                                <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $file->updated_at->format('d/m/Y H:i') }}</td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Chưa có tệp nào được đính kèm.</p>
                        @endif
                    </div>

                    <div class="card-footer text-end">
                        <button class="btn btn-primary btn-sm">
                            <i class="bi bi-upload"></i> Tải lên tệp
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comments --}}
        <div class="col-md-12 col-sm-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Comment of Task</h3>
                    <button class="btn btn-warning btn-sm float-right" type="button" data-toggle="collapse"
                        data-target="#newCommentForm" aria-expanded="false" aria-controls="newCommentForm">
                        <i class="fa fa-plus me-1"></i> New Comment
                    </button>
                </div>
                <div class="card-body py-2">
                    {{-- Form comment --}}
                    <div class="collapse mb-3" id="newCommentForm">
                        <form action="{{ route('comments.store', ['task' => $task->id]) }}" method="POST">
                            @csrf
                            <div class="position-relative mb-3" style="max-width: 100%;">
                                <textarea name="body" rows="3" class="form-control pe-5" placeholder="Write your comment..." required></textarea>
                                <button type="submit" class="btn btn-success btn-sm position-absolute"
                                    style="bottom: 5px; right: 10px;">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    {{-- List comment --}}
                    @forelse ($comments ??[] as $item)
                        {{-- {{ dd($item) }} --}}
                        <div class="direct-chat-msg">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-left">
                                    {{ $item->user?->name }}
                                    @if ($item->user?->role === 'client')
                                        <i class="mx-1 text-warning fa fa-user" aria-hidden="true"></i>
                                        Client
                                    @else
                                        <i class="mx-1 text-warning fa fa-location-arrow" aria-hidden="true"></i>
                                        {{ $item->user?->position }}
                                        <i class="mx-1 text-info fa fa-building" aria-hidden="true"></i>
                                        {{ $item->user?->department }}
                                    @endif

                                </span>
                                </span>
                                <span class="direct-chat-timestamp float-right" data-toggle="tooltip" data-placement="top"
                                    title="{{ $item->created_at?->format('d/m/Y H:i:s') ?? '--' }}">
                                    {{ $item->updated_at?->diffForHumans() ?? '--' }}
                                </span>
                            </div>

                            <img class="direct-chat-img" src="{{ asset('images/users/' . $item->user?->image) }}"
                                alt="{{ $item->user?->name }}">

                            {{-- phần chỉnh sửa/xóa comment --}}
                            <div class="position-relative comment-box" data-id="{{ $item->id }}">
                                {{-- Nội dung bình thường --}}
                                <div class="direct-chat-text" id="comment-text-{{ $item->id }}">{{ $item->body }}
                                </div>

                                {{-- Textarea edit (ẩn) --}}
                                <textarea class="form-control d-none comment-edit-textarea" rows="3" id="comment-textarea-{{ $item->id }}"
                                    required onchange="this.value = this.value.trim()">{{ $item->body }}</textarea>

                                {{-- Nút Save + Cancel --}}
                                <div class="mt-1">
                                    <button type="button" class="btn btn-sm btn-success d-none save-comment-btn"
                                        data-id="{{ $item->id }}"
                                        data-url="{{ route('comments.update', ['comment' => $item->id]) }}">
                                        <i class="fa fa-save"></i></button>
                                    <button class="btn btn-sm btn-secondary d-none cancel-edit-btn"
                                        data-id="{{ $item->id }}"><i class="fa fa-times"></i></button>
                                </div>

                                {{-- Dropdown menu --}}
                                <div class="dropdown position-absolute" style="bottom: -5px; right: 0;"
                                    id="dropdown-{{ $item->id }}">
                                    <button class="btn btn-sm text-warning bg-transparent" type="button"
                                        data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><button class="dropdown-item edit-comment-btn"
                                                data-id="{{ $item->id }}">Edit</button></li>
                                        <li>
                                            <form action="{{ route('comments.soft-delete', ['comment' => $item->id]) }}"
                                                method="POST"
                                                onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?',confirmButtonText: 'yes'})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                        </div>
                    @empty
                        <div class="direct-chat-msg">
                            <div class="direct-chat-infos clearfix">No comment in tasks</div>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleEditMode = (id, editing) => {
                const textDiv = document.getElementById(`comment-text-${id}`);
                const textarea = document.getElementById(`comment-textarea-${id}`);
                const saveBtn = document.querySelector(`.save-comment-btn[data-id="${id}"]`);
                const cancelBtn = document.querySelector(`.cancel-edit-btn[data-id="${id}"]`);
                const dropdown = document.getElementById(`dropdown-${id}`);

                if (editing) {
                    textarea.setAttribute('data-original', textarea.value);
                    textDiv.classList.add('d-none');
                    textarea.classList.remove('d-none');
                    saveBtn.classList.remove('d-none');
                    cancelBtn.classList.remove('d-none');
                    dropdown.classList.add('d-none');
                    textarea.focus();
                } else {
                    textarea.classList.add('d-none');
                    textDiv.classList.remove('d-none');
                    saveBtn.classList.add('d-none');
                    cancelBtn.classList.add('d-none');
                    dropdown.classList.remove('d-none');
                }
            };

            document.querySelectorAll('.edit-comment-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    toggleEditMode(btn.dataset.id, true);
                });
            });

            document.querySelectorAll('.save-comment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const url = this.dataset.url;

                    const textarea = document.getElementById(`comment-textarea-${id}`);
                    if (!textarea) {
                        console.error(`Textarea with id comment-textarea-${id} not found.`);
                        return;
                    }
                    console.log(url);

                    const newBody = textarea.value.trim();

                    $.ajax({
                        url: url,
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        data: {
                            body: newBody,
                        },
                        success: function(data) {
                            document.getElementById(`comment-text-${id}`).innerText =
                                newBody;
                            toggleEditMode(id, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Comment updated!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed to update!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // console.error(xhr.responseText);
                        }
                    });
                });
            });

            document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const textarea = document.getElementById(`comment-textarea-${id}`);
                    textarea.value = textarea.getAttribute('data-original');
                    toggleEditMode(id, false);
                });
            });

            // highlight comment
            const urlParams = new URLSearchParams(window.location.search);
            const commentId = urlParams.get('comment_id');

            if (commentId) {
                const target = document.getElementById(`comment-text-${commentId}`);
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
        });
    </script>
@endpush
