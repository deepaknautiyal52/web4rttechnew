@extends('layouts.admin')

@section('content')
<div class="admin-empty-state">
    <p>Your role does not have access to this section.</p>
    <p style="margin-top: 12px">
        <a href="{{ route('admin.overview') }}" class="admin-panel-link">← Back to overview</a>
    </p>
</div>
@endsection
