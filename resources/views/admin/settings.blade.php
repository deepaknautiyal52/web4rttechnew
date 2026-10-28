@extends('layouts.admin', ['title' => 'Settings'])
@php
    use App\Support\Meta;
    $user = auth()->user();
    $initials = strtoupper(collect(explode(' ', $user->name ?: 'A'))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode(''));
@endphp

@section('content')
<div class="admin-page-header">
    @include('admin.partials.page-title', ['title' => 'Settings', 'subtitle' => 'Manage your admin account security.'])
</div>

<div class="settings-grid">
    <div class="admin-panel settings-profile">
        <div class="settings-profile-avatar">{{ $initials }}</div>
        <h2>{{ $user->name ?: 'Admin' }}</h2>
        <x-badge :meta="Meta::ROLE_META" :value="$user->role ?: 'admin'" />
        <ul class="settings-profile-meta">
            <li>@include('admin.partials.icon', ['name' => 'mail', 'class' => '']) {{ $user->email }}</li>
            <li>@include('admin.partials.icon', ['name' => 'calendar', 'class' => '']) Member since {{ $user->created_at?->format('M Y') ?? '—' }}</li>
        </ul>
    </div>

    <div class="admin-panel admin-settings-panel">
        <div class="settings-panel-head">
            <span class="settings-panel-icon">@include('admin.partials.icon', ['name' => 'lock', 'class' => ''])</span>
            <div>
                <h2>Change Password</h2>
                <p class="admin-settings-hint">
                    Choose a strong password. You'll stay signed in on this device; other sessions will be
                    signed out.
                </p>
            </div>
        </div>

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

            <div class="resource-form-grid">
                <div class="admin-form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required minlength="8" placeholder="At least 8 characters" />
                    @error('password')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="admin-form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter new password" />
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-settings-btn">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
