@props(['meta', 'value'])
@php
    $m = $meta[$value] ?? ['label' => ($value === null || $value === '' ? '—' : $value), 'color' => '#898781'];
@endphp
<span class="status-badge" style="background-color: {{ $m['color'] }}1a; color: {{ $m['color'] }}"><span class="status-badge-dot" style="background-color: {{ $m['color'] }}"></span>{{ $m['label'] }}</span>
