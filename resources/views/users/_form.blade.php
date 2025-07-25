<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="col-md-12 col-sm-12">
    @csrf
    @if (!$is_create)
        @method('PUT')
    @endif
    <div class="card card-{{ $is_create ? 'success' : 'warning' }} w-100">
        <div class="card-header">{{ $is_create ? 'Information of new member' : 'Profile Information' }}</div>
        <div class="w-100 card-body row justify-content-center">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name ?? '') }}" required autocomplete="on"
                        onchange="this.value = this.value.trim()">
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email ?? '') }}" required autocomplete="on"
                        onchange="this.value = this.value.trim()">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                @if (!$user)
                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            autocomplete="current-password" onchange="this.value = this.value.trim()">
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
                <div class="form-group">
                    <label for="image">Image</label>
                    <div class="rounded" style="border: solid 1px #6c757d;">
                        @if ($user && $user->image)
                            <div class="mb-2">
                                <img src="{{ asset('images/users/' . $user->image) }}" alt="{{ $user->name }}"
                                    width="100px" />
                            </div>
                        @endif
                        <input type="file" name="image" id="image"
                            class="form-control-file @error('image') is-invalid @enderror">
                    </div>
                    @error('image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="position">Position</label>
                    <select name="position" id="position" class="form-control @error('position') is-invalid @enderror">
                        <option value="">-- Select Position --</option>
                        @foreach ($positions as $pos)
                            <option value="{{ $pos }}"
                                {{ old('position', $user->position ?? '') == $pos ? 'selected' : '' }}>
                                {{ ucfirst($pos) }}
                            </option>
                        @endforeach
                    </select>
                    @error('position')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select name="department" id="department"
                        class="form-control @error('department') is-invalid @enderror">
                        <option value="">-- Select Department --</option>
                        @foreach ($departments as $dep)
                            <option value="{{ $dep }}"
                                {{ old('department', $user->department ?? '') == $dep ? 'selected' : '' }}>
                                {{ ucfirst($dep) }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach (['active', 'inactive'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', $user->status ?? 'active') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}"
                                {{ old('role', $user->role ?? 'client') == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <button type="submit"
                class="btn btn-{{ $is_create ? 'success' : 'warning' }}">{{ $is_create ? 'Create' : 'Update' }}</button>
        </div>
    </div>
</form>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
