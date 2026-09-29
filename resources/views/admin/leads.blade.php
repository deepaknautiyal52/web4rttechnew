@extends('layouts.admin', ['title' => 'Leads'])
@php
    use App\Support\Meta;
    use App\Support\Site;
    $today = today()->toDateString();
@endphp

@section('content')
<div class="admin-page-header">
    <div>
        <h1>Leads</h1>
        <p>Everyone who has reached out through the site, and where each deal stands.</p>
    </div>
    <div class="admin-header-actions">
        <span class="admin-total-count">{{ $page->total() }} total</span>
        <a href="{{ route('admin.export', 'leads') }}" class="admin-secondary-btn">⬇ Export CSV</a>
    </div>
</div>

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by name, email, company, or subject...',
    'filters' => [
        ['name' => 'view', 'label' => 'View', 'all' => 'All leads', 'options' => ['follow_up' => 'Follow-ups due', 'stale' => 'New for more than 48h']],
        ['name' => 'status', 'label' => 'Status', 'options' => Meta::options(Meta::LEAD_STATUS)],
    ],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'No leads match these filters.' : 'No contact submissions yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Follow-up</th>
                    <th>Received</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $c)
                    @php
                        $followUp = $c->next_follow_up_at ? $c->next_follow_up_at->toDateString() : '';
                        $followUpDue = $followUp && $followUp <= $today && !in_array($c->status, ['won', 'lost'], true);
                        $isOpen = (string) $openId === (string) $c->id;
                        $useOld = $isOpen && old('_lead') === 'deal';
                        $draftStatus = $useOld ? old('status') : $c->status;
                    @endphp
                    <tr class="admin-table-row" data-expand="lead-{{ $c->id }}">
                        <td>
                            {{ $c->name }}
                            @if ($c->activities_count > 0)
                                <span class="lead-activity-count" title="Logged activities">{{ $c->activities_count }}</span>
                            @endif
                        </td>
                        <td><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></td>
                        <td>{{ Site::serviceTitle($c->service_interested) }}</td>
                        <td>{{ $c->subject }}</td>
                        <td><x-badge :meta="Meta::LEAD_STATUS" :value="$c->status" /></td>
                        <td class="{{ $followUpDue ? 'expense-cell' : '' }}">{{ $followUp ? Meta::date($followUp) : '—' }}</td>
                        <td>{{ Meta::dateTime($c->created_at) }}</td>
                    </tr>
                    <tr class="admin-table-detail-row" id="lead-{{ $c->id }}" @if (!$isOpen) hidden @endif>
                        <td colspan="7">
                            <div class="admin-detail-meta">
                                <span><strong>Phone:</strong> {{ $c->phone ?: '—' }}</span>
                                <span><strong>Company:</strong> {{ $c->company ?: '—' }}</span>
                                <span><strong>Budget:</strong> {{ $c->budget_range ?: '—' }}</span>
                                <span><strong>Timeline:</strong> {{ $c->timeline ?: '—' }}</span>
                                @if ($c->lost_reason)
                                    <span><strong>Lost because:</strong> {{ $c->lost_reason }}</span>
                                @endif
                            </div>
                            <strong>Message:</strong>
                            <p>{{ $c->message }}</p>

                            <form method="POST" action="{{ route('admin.leads.update', $c->id) }}" class="deal-edit" data-deal-form data-submitting-label="Saving...">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="_back" value="{{ $listUrl }}" />
                                <input type="hidden" name="_open" value="{{ $c->id }}" />
                                <input type="hidden" name="_lead" value="deal" />
                                <div class="deal-edit-field">
                                    <label>Status</label>
                                    <select name="status" data-deal-status>
                                        @foreach (Meta::LEAD_STATUS as $value => $meta)
                                            <option value="{{ $value }}" @selected($draftStatus === $value)>{{ $meta['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="deal-edit-field" data-show-for="won" @if ($draftStatus !== 'won') hidden @endif>
                                    <label>Deal Value ($)</label>
                                    <input type="number" min="0" step="0.01" placeholder="0.00" name="deal_value" value="{{ $useOld ? old('deal_value') : $c->deal_value }}" />
                                </div>

                                <div class="deal-edit-field" data-show-for="lost" @if ($draftStatus !== 'lost') hidden @endif>
                                    <label>Reason lost</label>
                                    @php $reason = $useOld ? old('lost_reason') : $c->lost_reason; @endphp
                                    <select name="lost_reason">
                                        <option value="">Select...</option>
                                        @foreach (Meta::LOST_REASONS as $r)
                                            <option value="{{ $r }}" @selected($reason === $r)>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="deal-edit-field" data-show-for="open" @if (in_array($draftStatus, ['won', 'lost'], true)) hidden @endif>
                                    <label>Next follow-up</label>
                                    <input type="date" name="next_follow_up_at" value="{{ $useOld ? old('next_follow_up_at') : $followUp }}" />
                                </div>

                                <button type="submit" class="deal-save-btn">Save</button>

                                @if (session('saved') == $c->id)
                                    <span class="deal-saved-check" data-autohide>✓ Saved</span>
                                @endif

                                <div class="lead-actions">
                                    @if (auth()->user()->canAccess('quotations'))
                                        <a class="admin-secondary-btn" href="{{ route('admin.quotations.index') }}?{{ http_build_query(['create' => 1, 'prefill' => ['contact_id' => $c->id, 'client_id' => $c->client_id ?: '', 'title' => $c->subject]]) }}">Create quotation</a>
                                    @endif
                                    @if ($c->client_id)
                                        <a class="admin-secondary-btn" href="{{ route('admin.clients.show', $c->client_id) }}">View client</a>
                                    @elseif ($c->status === 'won')
                                        <button type="submit" form="convert-{{ $c->id }}" class="admin-add-btn">Convert to client</button>
                                    @endif
                                </div>
                            </form>
                            @if (!$c->client_id && $c->status === 'won')
                                <form method="POST" action="{{ route('admin.leads.convert', $c->id) }}" id="convert-{{ $c->id }}" hidden>@csrf</form>
                            @endif
                            @if ($useOld && $errors->any())
                                <div class="deal-save-error">{{ $errors->first() }}</div>
                            @endif

                            <div class="lead-activity">
                                <strong>Activity</strong>
                                <form method="POST" action="{{ route('admin.leads.activities.store', $c->id) }}" class="lead-activity-form" data-activity-form>
                                    @csrf
                                    <input type="hidden" name="_back" value="{{ $listUrl }}" />
                                    <input type="hidden" name="_open" value="{{ $c->id }}" />
                                    <input type="hidden" name="_lead" value="activity" />
                                    <select name="type">
                                        @foreach (Meta::ACTIVITY_TYPE as $value => $m)
                                            <option value="{{ $value }}" @selected(($isOpen && old('_lead') === 'activity' ? old('type') : 'call') === $value)>{{ $m['icon'] }} {{ $m['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="body" placeholder="What happened? e.g. Called, they want a quote by Friday" value="{{ $isOpen && old('_lead') === 'activity' ? old('body') : '' }}" />
                                    <label class="lead-activity-followup">
                                        Next follow-up
                                        <input type="date" name="next_follow_up_at" />
                                    </label>
                                    <button type="submit" class="deal-save-btn" disabled>Log</button>
                                </form>
                                @if ($isOpen && old('_lead') === 'activity' && $errors->any())
                                    <div class="deal-save-error">{{ $errors->first() }}</div>
                                @endif

                                @if ($c->activities->isEmpty())
                                    <p class="resource-muted">No activity logged yet.</p>
                                @else
                                    <ul class="lead-timeline">
                                        @foreach ($c->activities as $a)
                                            <li>
                                                <span class="lead-timeline-icon">{{ Meta::ACTIVITY_TYPE[$a->type]['icon'] ?? '•' }}</span>
                                                <div>
                                                    <div class="lead-timeline-body">{{ $a->body }}</div>
                                                    <div class="lead-timeline-meta">
                                                        {{ Meta::ACTIVITY_TYPE[$a->type]['label'] ?? '' }} · {{ $a->user->name ?? 'Unknown' }} · {{ Meta::dateTime($a->created_at) }}
                                                        <form method="POST" action="{{ route('admin.leads.activities.destroy', [$c->id, $a->id]) }}" style="display:inline" data-confirm="Delete this activity?">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="_back" value="{{ $listUrl }}" />
                                                            <button type="submit" title="Delete">×</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if ($c->quotations->isNotEmpty())
                                    <p class="lead-quotes">
                                        Quotations: {{ $c->quotations->map(fn ($q) => $q->number.' ('.$q->status.')')->implode(', ') }}
                                    </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('admin.resource.pagination', ['always' => true])
@endif
@endsection
