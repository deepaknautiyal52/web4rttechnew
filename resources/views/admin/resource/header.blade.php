{{-- Page title, total count, CSV export and "+ Add" button. --}}
@php
    $createUrl = $listUrl.(str_contains($listUrl, '?') ? '&' : '?').'create=1';
@endphp
<div class="admin-page-header">
    @include('admin.partials.page-title', ['title' => $title, 'subtitle' => $subtitle ?? null])
    <div class="admin-header-actions">
        @isset($total)
            <span class="admin-total-count">{{ $total }} total</span>
        @endisset
        @if (!empty($exportType))
            <a href="{{ route('admin.export', $exportType) }}" class="admin-secondary-btn">@include('admin.partials.icon', ['name' => 'download', 'class' => 'admin-btn-icon']) Export CSV</a>
        @endif
        @if (!empty($addLabel))
            <a href="{{ $createUrl }}" class="admin-add-btn">{{ $addLabel }}</a>
        @endif
    </div>
</div>

@if ($errors->any() && empty($formOpen) && empty($suppressErrorBanner))
    <div class="admin-dashboard-error">{{ $errors->first() }}</div>
@endif
@if (session('notice'))
    <div class="form-alert form-alert-success" data-autohide>{{ session('notice') }}</div>
@endif
