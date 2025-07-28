<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="col-md-12 col-sm-12">
    @csrf
    @if (!$isCreate)
        @method('PUT')
    @endif
    <div class="card card-{{ $isCreate ? 'success' : 'warning' }} w-100">
        <div class="card-header">{{ $isCreate ? 'New Task Information' : 'Task Details' }}</div>
        <div class="w-100 card-body row justify-content-center">
            <div class="col-md-6 col-sm-12">
                {{-- Task Name --}}
                <div class="form-group">
                    <label for="name">Task Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $task->name ?? '') }}" required autocomplete="on"
                        {{ $canUpdate($task) ? '' : 'disabled' }}>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- project name --}}
                <div class="form-group">
                    <label for="project_id">Project Name <span class="text-danger">*</span></label>
                    <select name="project_id" id="project_id"
                        class="form-control @error('project_id') is-invalid @enderror"
                        {{ $canUpdate($task) ? '' : 'disabled' }}>
                        <option value="">-- Select Project Name --</option>
                        @foreach ($projects as $id => $name)
                            <option value="{{ $id }}"
                                {{ old('project_id', $task->project?->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror" {{ $canUpdate($task) ? '' : 'disabled' }}>{{ old('description', $task->description ?? '') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                
                {{-- Created By --}}

                @if ($exceptClient)
                    <div class="form-group">
                        <label for="created_by">Created By</label>
                        <select name="created_by" id="created_by"
                            class="form-control @error('created_by') is-invalid @enderror"
                            {{ $canUpdate($task) ? '' : 'disabled' }}>
                            <option value="">-- Select Creator --</option>
                            @foreach ($createdUsers as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('created_by', $task->created_by ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                        @error('created_by')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

            </div>

            <div class="col-md-6 col-sm-12">
                {{-- Assignee + Issue Type --}}
                @if ($exceptClient)
                    <div class="form-group">
                        <label for="assigned_to">Assignee</label>
                        <select name="assigned_to" id="assigned_to"
                            class="form-control @error('assigned_to') is-invalid @enderror"
                            {{ $canUpdate($task) ? '' : 'disabled' }}
                            >
                            <option value="">-- Select Assignee --</option>
                            @foreach ($assignedUsers as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('assigned_to', $task->assigned_to ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="issue_type_id">Issue Type</label>
                        <select name="issue_type_id" id="issue_type_id"
                            class="form-control @error('issue_type_id') is-invalid @enderror"
                            {{ $canUpdate($task) ? '' : 'disabled' }}>
                            <option value="">-- Select Issue Type --</option>
                            @foreach ($issueTypes as $value => $name)
                                <option value="{{ $value }}"
                                    {{ old('issue_type_id', $task->issue_type_id ?? '') == $value ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                        @error('issue_type_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                {{-- Status --}}
                <div class="form-group">
                    <label for="status_id">Status</label>
                    <select name="status_id" id="status_id"
                        class="form-control @error('status_id') is-invalid @enderror"
                        {{ $canUpdate($task) ? '' : 'disabled' }}>
                        <option value="">-- Select Status --</option>
                        @foreach ($statuses as $value => $name)
                            <option value="{{ $value }}"
                                {{ old('status_id', $task->status_id ?? '') == $value ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @error('status_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Start Date --}}
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date', $task->start_date ?? '') }}"
                        {{ $canUpdate($task) ? '' : 'disabled' }}>
                    @error('start_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Due Date --}}
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date', $task->due_date ?? '') }}"
                        {{ $canUpdate($task) ? '' : 'disabled' }}>
                    @error('due_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            @if ($canUpdate($task))
                <button type="submit"
                    class="btn btn-{{ $isCreate ? 'success' : 'warning' }}">{{ $isCreate ? 'Create Task' : 'Update Task' }}</button>
            @endif
        </div>
    </div>
</form>
@push('js')
    <script>
        // ẩn thông báo lỗi khi focus input
        $(document).ready(function() {
            $('.form-control').on('focus', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').hide();
            });

            const invalidInput = document.querySelector('.is-invalid');
            if (invalidInput) {
                const form = invalidInput.closest('form');
                if (form) {
                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    </script>
@endpush
