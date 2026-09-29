@extends('layouts.admin', ['title' => 'News & Articles'])
@php use App\Support\Meta; @endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'News & Articles',
    'subtitle' => 'Posts shown in the public Newsroom. Publishing here updates the site immediately, with no code deploy.',
    'total' => $page->total(),
    'addLabel' => '+ New Article',
])

@include('admin.resource.form', [
    'formTitle' => 'Article',
    'storeUrl' => route('admin.news.store'),
    'updateUrl' => $editing ? route('admin.news.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search articles...',
    'filters' => [['name' => 'is_published', 'label' => 'Status', 'options' => ['1' => 'Published', '0' => 'Draft']]],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $a)
                    <tr>
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $a->title }}</strong>
                                <span>/news/{{ $a->slug }}</span>
                            </div>
                        </td>
                        <td>{{ Meta::date($a->published_at) }}</td>
                        <td><x-badge :meta="Meta::PUBLISHED" :value="$a->is_published ? 1 : 0" /></td>
                        @include('admin.resource.actions', [
                            'row' => $a,
                            'deleteUrl' => route('admin.news.destroy', $a->id),
                            'deleteLabel' => $a->title,
                            'before' => $a->is_published ? '<button type="button" title="View on site" data-href="'.e(url('/news/'.$a->slug)).'" data-target="_blank">🔗</button>' : '',
                        ])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
