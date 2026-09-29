<?php

namespace App\Support;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\Project;

/**
 * Id/name lists for the admin panel's dropdowns. Available to every admin
 * role and deliberately free of sensitive fields (no salaries, no money).
 */
class Lookups
{
    public static function all(): array
    {
        return [
            'clients' => Client::orderByRaw('COALESCE(company, name)')->get(['id', 'name', 'company', 'status']),
            'projects' => Project::orderBy('name')->get(['id', 'name', 'client_id', 'status', 'currency']),
            'employees' => Employee::orderBy('name')->get(['id', 'name', 'status']),
            'contacts' => Contact::whereNotIn('status', ['lost'])->latest()->limit(300)
                ->get(['id', 'name', 'company', 'email', 'status', 'client_id']),
        ];
    }

    public static function clientOptions(array $l): array
    {
        return $l['clients']->mapWithKeys(fn ($c) => [$c->id => $c->company ?: $c->name])->all();
    }

    public static function activeEmployeeOptions(array $l): array
    {
        return $l['employees']->where('status', 'active')->mapWithKeys(fn ($e) => [$e->id => $e->name])->all();
    }

    /** Project options tagged with their client, so the form can filter them by the chosen client. */
    public static function projectOptions(array $l, bool $withClient = false): array
    {
        $clients = $l['clients']->keyBy('id');

        return $l['projects']->map(function ($p) use ($clients, $withClient) {
            $client = $clients[$p->client_id] ?? null;

            return [
                'value' => $p->id,
                'label' => $p->name.($withClient && $client ? ' ('.($client->company ?: $client->name).')' : ''),
                'parent' => $p->client_id,
            ];
        })->all();
    }

    public static function contactOptions(array $l): array
    {
        return $l['contacts']->mapWithKeys(fn ($c) => [$c->id => $c->name.($c->company ? " ({$c->company})" : '')])->all();
    }
}
