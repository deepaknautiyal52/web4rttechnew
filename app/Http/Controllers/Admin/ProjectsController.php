<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ProjectController;
use App\Support\Lookups;
use App\Support\Meta;
use App\Support\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ProjectsController extends ResourcePageController
{
    protected string $api = ProjectController::class;

    protected string $view = 'admin.projects';

    protected bool $needsLookups = true;

    protected function emptyForm(): array
    {
        return ['name' => '', 'client_id' => '', 'service_id' => '', 'status' => 'planning', 'start_date' => today()->toDateString(), 'deadline' => '', 'budget' => '', 'other_costs' => '', 'currency' => 'INR', 'employee_id' => '', 'description' => ''];
    }

    protected function toForm(Model $p): array
    {
        return array_merge(parent::toForm($p), [
            'client_id' => (string) $p->client_id,
            'employee_id' => (string) ($p->employee_id ?? ''),
        ]);
    }

    protected function extra(Request $request, array $l): array
    {
        $services = collect(Site::services())->mapWithKeys(fn ($s) => [$s['id'] => $s['title']])->all();

        return ['fields' => [
            ['name' => 'name', 'label' => 'Project name', 'required' => true, 'placeholder' => 'e.g. Acme corporate website'],
            ['name' => 'client_id', 'label' => 'Client', 'type' => 'select', 'required' => true, 'placeholder' => 'Select a client...', 'options' => Lookups::clientOptions($l)],
            ['name' => 'service_id', 'label' => 'Service', 'type' => 'select', 'placeholder' => 'Not specified', 'options' => $services],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Meta::options(Meta::PROJECT_STATUS)],
            ['name' => 'start_date', 'label' => 'Start date', 'type' => 'date'],
            ['name' => 'deadline', 'label' => 'Deadline', 'type' => 'date'],
            ['name' => 'budget', 'label' => 'Budget', 'type' => 'number', 'help' => 'What the client is paying / your cost ceiling.'],
            ['name' => 'other_costs', 'label' => 'Other costs', 'type' => 'number', 'help' => 'Licences, freelancers, etc. Staff time is added from timesheets.'],
            ['name' => 'currency', 'label' => 'Currency', 'type' => 'select', 'options' => array_combine(Meta::CURRENCIES, Meta::CURRENCIES)],
            ['name' => 'employee_id', 'label' => 'Project lead', 'type' => 'select', 'placeholder' => 'Unassigned', 'options' => Lookups::activeEmployeeOptions($l)],
            ['name' => 'description', 'label' => 'Description / scope', 'type' => 'textarea', 'full' => true],
        ]];
    }
}
