@extends('layouts.admin', ['title' => 'Settings'])

@section('content')
<div class="admin-page-header">
    <div>
        <h1>Settings</h1>
        <p>Manage your admin account security.</p>
    </div>
</div>

<div class="admin-panel admin-settings-panel">
    <h2>Change Password</h2>
    <p class="admin-settings-hint">
        Choose a strong password. You'll stay signed in on this device; other sessions will be
        signed out.
    </p>

    @if (session('notice'))
        <div class="form-alert form-alert-success">{{ session('notice') }}</div>
    @endif
    @if ($errors->any())
        <div class="form-alert form-alert-error">Please check the highlighted fields and try again.</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.password') }}" novalidate class="admin-settings-form" data-submitting-label="Updating...">
        @csrf
        @method('PUT')
        <div class="admin-form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required placeholder="••••••••" />
            @error('current_password')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="admin-form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="At least 8 characters" />
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="admin-form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter new password" />
        </div>

        <button type="submit" class="admin-settings-btn">Update Password</button>
    </form>
</div>
@endsection
