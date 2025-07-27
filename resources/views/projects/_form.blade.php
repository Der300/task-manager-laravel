<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="col-md-12 col-sm-12">
    @csrf
    @if (!$isCreate)
        @method('PUT')
    @endif
    <div class="card card-{{ $isCreate ? 'success' : 'warning' }} w-100">
        <div class="card-header">{{ $isCreate ? 'New Project Information' : 'Project Details' }}</div>
        <div class="w-100 card-body row justify-content-center">
            <div class="col-md-6 col-sm-12">
                {{-- Project Name --}}
                <div class="form-group">
                    <label for="name">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $project->name ?? '') }}" required autocomplete="on"
                        {{ $canUpdate($project) ? '' : 'disabled' }}>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Slug --}}
                @if ($exceptClient)
                    <div class="form-group">
                        <label for="slug">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug"
                            class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $project->slug ?? '') }}" required {{ $canUpdate($project) ? '' : 'disabled' }}>
                        @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                {{-- Description --}}
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror" {{ $canUpdate($project) ? '' : 'disabled' }}>{{ old('description', $project->description ?? '') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Client + Created By --}}
                @if ($exceptClient)
                    <div class="form-group">
                        <label for="client_id">Client</label>
                        <select name="client_id" id="client_id"
                            class="form-control @error('client_id') is-invalid @enderror"
                            {{ $canUpdate($project) ? '' : 'disabled' }}>
                            <option value="">-- Select Client --</option>
                            @foreach ($clients as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('client_id', $project->client_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="created_by">Created By</label>
                        <select name="created_by" id="created_by"
                            class="form-control @error('created_by') is-invalid @enderror"
                            {{ $canUpdate($project) ? '' : 'disabled' }}>
                            <option value="">-- Select Creator --</option>
                            @foreach ($createdUsers as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('created_by', $project->created_by ?? '') == $id ? 'selected' : '' }}>
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
                            {{ $canUpdate($project) ? '' : 'disabled' }}>
                            <option value="">-- Select Assignee --</option>
                            @foreach ($assignedUsers as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('assigned_to', $project->assigned_to ?? '') == $id ? 'selected' : '' }}>
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
                            {{ $canUpdate($project) ? '' : 'disabled' }}>
                            <option value="">-- Select Issue Type --</option>
                            @foreach ($issueTypes as $value => $name)
                                <option value="{{ $value }}"
                                    {{ old('issue_type_id', $project->issue_type_id ?? '') == $value ? 'selected' : '' }}>
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
                        {{ $canUpdate($project) ? '' : 'disabled' }}>
                        <option value="">-- Select Status --</option>
                        @foreach ($statuses as $value => $name)
                            <option value="{{ $value }}"
                                {{ old('status_id', $project->status_id ?? '') == $value ? 'selected' : '' }}>
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
                        value="{{ old('start_date', $project->start_date ?? '') }}" {{ $canUpdate($project) ? '' : 'disabled' }}>
                    @error('start_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Due Date --}}
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                        class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date', $project->due_date ?? '') }}" {{ $canUpdate($project) ? '' : 'disabled' }}>
                    @error('due_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            @if ($canUpdate($project))
                <button type="submit"
                    class="btn btn-{{ $isCreate ? 'success' : 'warning' }}">{{ $isCreate ? 'Create Project' : 'Update Project' }}</button>
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

            // tạo slug
            let debounceTimer;

            $('#name').on('keyup', function() {
                clearTimeout(debounceTimer); // Hủy timer cũ nếu còn

                let valueName = $(this).val();

                debounceTimer = setTimeout(function() {
                    $.ajax({
                        method: 'get',
                        url: "{{ route('projects.make_slug') }}",
                        data: {
                            slug: valueName
                        },
                        success: function(response) {
                            $('#slug').val(response.slug);
                        }
                    }).fail(function() {
                        // alert("error");
                    });
                }, 500); // ⏱ đợi 500ms sau khi ngưng gõ mới gửi
            });
        });
    </script>
@endpush
