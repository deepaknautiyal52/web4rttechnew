@if ($days === null)
    —
@else
    <span class="days-pill{{ $days < 0 ? ' expired' : ($days <= 7 ? ' urgent' : ($days <= 30 ? ' soon' : '')) }}">{{ $days < 0 ? 'Expired '.(-$days).'d ago' : ($days === 0 ? 'Today' : $days.' days') }}</span>
@endif
