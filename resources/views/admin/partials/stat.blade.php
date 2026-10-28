{{--
    Stat card: label + icon, value, optional sub line. Becomes a link when $href is set.
    $tone: 'income-text' | 'expense-text' colours the value.
--}}
@php $tag = !empty($href) ? 'a' : 'div'; @endphp
<{{ $tag }} @if (!empty($href)) href="{{ $href }}" @endif class="admin-stat-card{{ !empty($href) ? ' attention-stat' : '' }}" style="--stat-accent: {{ $accent ?? '#4F46E5' }}">
    <span class="admin-stat-top">
        <span class="admin-stat-label">{{ $label }}</span>
        <span class="admin-stat-icon">@include('admin.partials.icon', ['name' => $icon, 'class' => ''])</span>
    </span>
    <span class="admin-stat-value{{ !empty($tone) ? ' admin-stat-value-flat '.$tone : '' }}">{{ $value }}</span>
    @if (isset($sub))
        <span class="admin-stat-sub">{{ $sub }}</span>
    @endif
</{{ $tag }}>
