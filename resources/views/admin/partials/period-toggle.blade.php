<div class="admin-period-toggle" role="tablist" aria-label="Chart time period" data-period-toggle>
    @foreach (['week' => 'Weekly', 'month' => 'Monthly', 'quarter' => 'Quarterly', 'half-year' => 'Half-Yearly', 'year' => 'Yearly'] as $value => $label)
        <button type="button" role="tab" aria-selected="{{ $value === 'month' ? 'true' : 'false' }}" class="admin-period-btn {{ $value === 'month' ? 'active' : '' }}" data-period="{{ $value }}">{{ $label }}</button>
    @endforeach
</div>
