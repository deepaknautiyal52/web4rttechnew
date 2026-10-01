@extends('layouts.admin', ['title' => 'Audit Log'])
@php
    use App\Support\Meta;
    $show = function ($v) {
        if ($v === null || $v === '') return '∅';
        if (is_array($v) || is_object($v)) return json_encode($v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (is_bool($v)) return $v ? 'true' : 'false';
        $s = (string) $v;
        return mb_strlen($s) > 80 ? mb_substr($s, 0, 80).'…' : $s;
    };
    $modelOptions = collect(Meta::AUDIT_MODELS)->mapWithKeys(fn ($m) => [$m => preg_replace('/([a-z])([A-Z])/', '$1 $2', $m)])->all();
@endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Audit Log',
    'subtitle' => 'Who created, changed or deleted what, and when. Click a row to see the details.',
    'total' => $page->total(),
])


@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by record name...',
    'filters' => [
        ['name' => 'model_type', 'label' => 'Record', 'options' => $modelOptions],
        ['name' => 'action', 'label' => 'Action', 'options' => Meta::options(Meta::AUDIT_ACTIONS)],
    ],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>By</th>
                    <th>Action</th>
                    <th>Record</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $l)
                    <tr class="admin-table-row" data-expand="audit-{{ $l->id }}">
                        <td>{{ Meta::dateTime($l->created_at) }}</td>
                        <td>{!! $l->user ? e($l->user->name) : '<span class="resource-muted">Website visitor / system</span>' !!}</td>
                        <td><x-badge :meta="Meta::AUDIT_ACTIONS" :value="$l->action" /></td>
                        <td>{{ $l->model_type }} #{{ $l->model_id }}</td>
                        <td>{{ $l->summary ?: '—' }}</td>
                    </tr>
                    <tr class="admin-table-detail-row resource-detail-row" id="audit-{{ $l->id }}" @if ((string) $openId !== (string) $l->id) hidden @endif>
                        <td colspan="5">
                            @php
                                $entries = collect($l->changes ?? [])->except(['id', 'created_at', 'updated_at']);
                            @endphp
                            @if ($entries->isEmpty())
                                <span class="resource-muted">—</span>
                            @else
                                <ul class="audit-changes">
                                    @foreach ($entries as $field => $value)
                                        <li>
                                            <strong>{{ $field }}</strong>:
                                            @if ($l->action === 'updated' && is_array($value) && array_key_exists('to', $value))
                                                <span class="audit-from">{{ $show($value['from'] ?? null) }}</span> → <span>{{ $show($value['to']) }}</span>
                                            @else
                                                {{ $show($value) }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
