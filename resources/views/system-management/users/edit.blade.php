@extends('layouts.app')

@section('title', 'Edit User')

@section('breadcrumb')
    <a href="{{ route('system-management.users.index') }}" class="breadcrumb-item">Users</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit User</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit User</h2>
            <p>Update user information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.users.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Avatar Upload -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Avatar</label>
                <input type="hidden" name="remove_avatar" id="removeAvatarFlag" value="0">
                <div class="avatar-upload-container">
                    <div class="avatar-preview-wrapper">
                        <img src="{{ $user->getAvatarUrl() }}" alt="Avatar" class="avatar-preview-img"
                            id="avatarPreviewImg">
                        <button type="button" class="avatar-remove-btn" id="removeAvatar"
                            style="{{ $user->hasAvatar() ? 'display: flex;' : 'display: none;' }}" title="Remove avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="avatar-upload-actions">
                        <input type="file" id="avatarInput" name="avatar" class="avatar-file-input"
                            accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
                        <button type="button" class="btn btn-secondary btn-sm" id="chooseAvatarBtn">Choose File</button>
                        <span class="avatar-hint">JPG, PNG, GIF, WebP or SVG. Max 2MB.</span>
                    </div>
                </div>
                @error('avatar')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name <span class="form-required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror"
                        value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span class="form-required">*</span></label>
                    <input type="email" id="email" name="email" class="form-input @error('email') error @enderror"
                        value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Role <span class="form-required">*</span></label>
                    @if ($canChangeRole)
                        <select id="role" name="role" class="form-input @error('role') error @enderror" required>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('role', $user->role) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-input" value="{{ $user->getRoleDisplayName() }}" disabled>
                        <span class="form-hint">System role cannot be changed</span>
                    @endif
                    @error('role')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status <span class="form-required">*</span></label>
                    @if ($canChangeStatus)
                        <select id="status" name="status" class="form-input @error('status') error @enderror"
                            required>
                            <option value="1"
                                {{ old('status', $user->status ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0"
                                {{ old('status', $user->status ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <span class="form-hint">Inactive status can't login to the system</span>
                    @else
                        <input type="text" class="form-input" value="{{ $user->getStatusDisplayName() }}" disabled>
                        <span class="form-hint">You cannot change your own status</span>
                    @endif
                    @error('status')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input id="phone" type="tel" name="phone_number" class="form-input"
                        value="{{ old('phone_number', $user->full_phone_number ?? '') }}"
                        placeholder="+1 123 456 7890"
                        autocomplete="tel">
                    <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', $user->country_code ?? '') }}">
                    @error('phone_number')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    @error('country_code')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" id="location" name="location"
                        class="form-input @error('location') error @enderror"
                        value="{{ old('location', $user->location) }}" placeholder="Enter location">
                    @error('location')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Birth Date</label>
                    <input type="date" id="birth_date" name="birth_date" class="form-input @error('birth_date') error @enderror"
                        value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}" placeholder="Enter birth date">
                    @error('birth_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="password" class="form-label">New Password <span class="form-optional">(leave blank to
                            keep current)</span></label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="form-input @error('password') error @enderror" placeholder="Enter new password">
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                        autocomplete="new-password" placeholder="Confirm new password">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Update
                </button>
                <a href="{{ route('system-management.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
