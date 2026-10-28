{{-- Edit / delete buttons for a table row. $slot-like extras go in $before. --}}
@php
    $editUrl = $listUrl.(str_contains($listUrl, '?') ? '&' : '?').'edit='.$row->id;
@endphp
<td class="finance-row-actions">
    {!! $before ?? '' !!}
    @if ($canEdit ?? true)
        <button type="button" data-href="{{ $editUrl }}" title="Edit" aria-label="Edit">@include('admin.partials.icon', ['name' => 'edit', 'class' => ''])</button>
    @endif
    @if ($canDelete ?? true)
        <form method="POST" action="{{ $deleteUrl }}" style="display:inline" data-confirm="Delete &quot;{{ $deleteLabel }}&quot;? This cannot be undone.">
            @csrf
            @method('DELETE')
            <input type="hidden" name="_back" value="{{ $listUrl }}" />
            <button type="submit" title="Delete" aria-label="Delete" class="row-action-danger">@include('admin.partials.icon', ['name' => 'trash', 'class' => ''])</button>
        </form>
    @endif
</td>
