<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\TicketController;
use App\Support\Lookups;
use App\Support\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TicketsController extends ResourcePageController
{
    protected string $api = TicketController::class;

    protected string $view = 'admin.tickets';

    protected array $defaultFilters = ['status' => 'open_all'];

    protected bool $needsLookups = true;

    protected function emptyForm(): array
    {
        return ['title' => '', 'client_id' => '', 'project_id' => '', 'priority' => 'medium', 'status' => 'open', 'employee_id' => '', 'description' => ''];
    }

    protected function toForm(Model $t): array
    {
        return [
            'title' => $t->title,
            'client_id' => (string) ($t->client_id ?? ''),
            'project_id' => (string) ($t->project_id ?? ''),
            'priority' => $t->priority,
            'status' => $t->status,
            'employee_id' => (string) ($t->employee_id ?? ''),
            'description' => $t->description ?? '',
        ];
    }

    protected function extra(Request $request, array $l): array
    {
        return ['fields' => [
            ['name' => 'title', 'label' => 'Title', 'required' => true, 'full' => true, 'placeholder' => 'e.g. Contact form not sending emails'],
            ['name' => 'client_id', 'label' => 'Client', 'type' => 'select', 'placeholder' => 'Internal / none', 'options' => Lookups::clientOptions($l)],
            ['name' => 'project_id', 'label' => 'Project', 'type' => 'select', 'placeholder' => 'None', 'options' => Lookups::projectOptions($l), 'dependsOn' => 'client_id'],
            ['name' => 'priority', 'label' => 'Priority', 'type' => 'select', 'options' => Meta::options(Meta::TICKET_PRIORITY), 'help' => 'SLA: urgent 4h, high 24h, medium 3 days, low 7 days from when the ticket was opened.'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Meta::options(Meta::TICKET_STATUS)],
            ['name' => 'employee_id', 'label' => 'Assigned to', 'type' => 'select', 'placeholder' => 'Unassigned', 'options' => Lookups::activeEmployeeOptions($l)],
            ['name' => 'description', 'label' => 'Details', 'type' => 'textarea', 'full' => true, 'rows' => 5],
        ]];
    }
}
