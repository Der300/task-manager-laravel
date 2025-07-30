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
        <div class="col-md-12 col-sm-12">
            <div class="card mb-3 card-primary">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#fileCollapse"
                    aria-expanded="true">
                    <h3 class="card-title">Attachment files</h3>
                </div>
                <div id="fileCollapse" class="collapse">
                    <div class="card-body" style="height: 300px; overflow-y:auto">
                        @if ($canUploadSoftDelFile($task))
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <button class="btn btn-warning btn-sm" type="button" data-toggle="collapse"
                                        data-target="#uploadFile" aria-expanded="false" aria-controls="uploadFile">
                                        <i class="fa fa-upload mr-1" aria-hidden="true"></i> Upload file
                                    </button>
                                </div>
                                <div class="card-body collapse" id="uploadFile">
                                    <form id="uploadFileForm" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="file" id="fileInput" required
                                            accept="image/*, video/*, audio/*, application/pdf, .doc,.docx, .xls, .xlsx, .ppt, .pptx, .txt, .zip, .rar, .7z, .tar">
                                        <input type="text" name="description" id="fileDesc"
                                            placeholder="Reason upload file">
                                        <button type="submit" class="btn-sm btn-primary" id="uploadFileBtn"
                                            data-url={{ route('myfiles.upload', ['task' => $task->id]) }}><i
                                                class="fa fa-upload" aria-hidden="true"></i></button>
                                        <button type="button" class="btn-sm btn-danger" data-toggle="collapse"
                                            data-target="#uploadFile" id="cancelUpload"><i class="fa fa-times"></i></button>
                                    </form>
                                    <div id="uploadProgressContainer" class="progress progress-sm my-2 d-none">
                                        <div id="uploadProgressBar" class="progress-bar bg-success" role="progressbar"
                                            style="width: 0%">0%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="file-table">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th class="align-middle">File</th>
                                        <th class="align-middle">Type</th>
                                        <th class="align-middle">Descrition</th>
                                        <th class="align-middle">Uploader</th>
                                        <th class="align-middle">Upload at</th>
                                        <th class="align-middle">Updated at</th>
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($files as $item)
                                        <tr>
                                            <td class="align-middle">{{ $item->original_name }}</td>
                                            <td class="align-middle">
                                                {{ app(App\Services\File\FileService::class)->getFileTypeLabel($item->mime_type) }}
                                            </td>
                                            <td class="align-middle">{{ $item->description }}</td>
                                            <td class="align-middle">{{ $item->uploader?->name }}</td>
                                            <td class="align-middle">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="align-middle">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    @can('softDelete', $item)
                                                        <form action="{{ route('myfiles.soft-delete', ['file' => $item->id]) }}"
                                                            method="POST" class="mx-1"
                                                            onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?'})">
                                                            @csrf
                                                            @method('DELETE')
                                                            <span data-toggle="tooltip" data-placement="top"
                                                                title="Move to Recycle">
                                                                <button class="btn btn-danger btn-sm px-2 py-1">
                                                                    <i class="fa fa-recycle" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                        </form>
                                                    @endcan
                                                    <a href="{{ route('myfiles.download', ['file' => $item->id]) }}"
                                                        class="btn btn-warning btn-sm px-2 py-1 mx-1" data-toggle="tooltip"
                                                        data-placement="top" title="Dowload files">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="tr_empty">
                                            <td colspan="7" class="text-center text-muted">No files attached yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="card-footer"></div>
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
                        <i class="fa fa-plus mr-1"></i> New Comment
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
                                <span class="direct-chat-timestamp float-right" data-toggle="tooltip"
                                    data-placement="top" title="{{ $item->created_at?->format('d/m/Y H:i:s') ?? '--' }}">
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
                                    <button type="button" class="btn btn-sm btn-secondary d-none cancel-edit-btn"
                                        data-id="{{ $item->id }}"><i class="fa fa-times"></i></button>
                                </div>

                                {{-- Dropdown menu --}}
                                @can('updateOrSoftDelete', $item)
                                    <div class="dropdown position-absolute" style="bottom: -5px; right: 0;"
                                        id="dropdown-{{ $item->id }}">
                                        <button class="btn btn-sm text-warning bg-transparent" type="button"
                                            data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-h"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('update', $item)
                                                {{-- gọi update trong comment policy --}}
                                                <li>
                                                    <button class="dropdown-item edit-comment-btn"
                                                        data-id="{{ $item->id }}">Edit
                                                    </button>
                                                </li>
                                            @endcan

                                            @can('softDelete', $item)
                                                <li>
                                                    <form action="{{ route('comments.soft-delete', ['comment' => $item->id]) }}"
                                                        method="POST"
                                                        onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?',confirmButtonText: 'yes'})">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                @endcan
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


            // upload file
            // Thông báo lỗi
            const maxFileSize = 50 * 1024 * 1024; // 50MB

            // Check file size
            $('#fileInput').on('change', function() {
                const file = this.files[0];
                if (file && file.size > maxFileSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File is too large!',
                        text: 'Please select a file smaller than 50MB.',
                        showConfirmButton: true,
                    });
                    $(this).val('');
                }
            });

            //fn chuyển mine_type
            function getFileTypeLabel(mime) {
                if (mime.startsWith('image/')) return 'Image';
                if (mime.startsWith('video/')) return 'Video';
                if (mime.startsWith('audio/')) return 'Audio';
                if (mime === 'application/pdf') return 'PDF';
                if (mime.includes('word') || mime.includes('officedocument.wordprocessingml')) return 'Word';
                if (mime.includes('excel') || mime.includes('spreadsheetml')) return 'Excel';
                if (mime.includes('powerpoint') || mime.includes('presentationml')) return 'PowerPoint';
                if (mime === 'text/plain') return 'Text File';
                if (mime.includes('zip') || mime.includes('rar') || mime.includes('7z')) return 'Archive';
                return 'Other';
            }

            // upload file
            $('#uploadFileBtn').on('click', function() {
                const file = document.getElementById('fileInput').files[0];
                if (!file) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please choose a file!'
                    });
                    return;
                }
                const url = this.dataset.url;
                const form = document.getElementById('uploadFileForm');
                const formData = new FormData(form);

                // upload file
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    xhr: function() {
                        let xhr = new XMLHttpRequest();

                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                let percent = Math.round((e.loaded / e.total) * 100);
                                $('#uploadProgressContainer').removeClass('d-none');
                                $('#uploadProgressBar').css('width', percent + '%')
                                    .text(percent + '%');
                            }
                        });

                        return xhr;
                    },
                    beforeSend: function() {
                        $('#uploadFileBtn').prop('disabled', true);
                        $('#uploadProgressContainer').removeClass('d-none');
                        $('#uploadProgressBar').css('width', '0%').text('0%');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'File has been upload successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        const file = response.file;

                        const typeLabel = getFileTypeLabel(file.mime_type);

                        const newRow = `
                                        <tr>
                                            <td class="align-middle">${file.original_name}</td>
                                            <td class="align-middle">${typeLabel}</td>
                                            <td class="align-middle">${file.description ?? ''}</td>
                                            <td class="align-middle">${file.uploader_name ?? '-'}</td>
                                            <td class="align-middle">${file.created_at}</td>
                                            <td class="align-middle">${file.updated_at}</td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <form action="/myfiles/${file.id}" method="POST" class="mx-1"
                                                        onsubmit="return swalConfirmWithForm(event, {title: 'Confirm Move to Recycle',text: 'Are you sure you want to move to recycle?'})">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm px-2 py-1">
                                                            <i class="fa fa-recycle" aria-hidden="true"></i>
                                                        </button>
                                                    </form>
                                                    <a href="/myfiles/${file.id}/download" class="btn btn-warning btn-sm px-2 py-1 mx-1"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="Download file">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    `;

                        $('#file-table tbody').prepend(newRow); // chèn lên đầu bảng

                        $('#file-table tbody .tr_empty').remove();
                        $('#uploadFileForm')[0].reset();
                        $('#uploadFile').collapse('hide');
                    },
                    error: function(xhr) {
                        if (xhr.status === 413) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File too large!',
                                text: 'Server rejected the file. Try smaller size.',
                                showConfirmButton: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed to upload file!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    complete: function() {
                        $('#uploadFileBtn').prop('disabled', false);
                        setTimeout(() => {
                            $('#uploadProgressContainer').addClass('d-none');
                            $('#uploadProgressBar').css('width', '0%').text('0%');
                        }, 1500);
                    }
                });
            });

            $('#cancelUpload').on('click', function() {
                $('#uploadFileForm')[0].reset();
                $('#uploadFile').collapse('hide');
            });
        });
    </script>
@endpush
