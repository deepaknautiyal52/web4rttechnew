<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\TimesheetController;
use App\Support\Lookups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TimesheetsController extends ResourcePageController
{
    protected string $api = TimesheetController::class;

    protected string $view = 'admin.timesheets';

    protected bool $needsLookups = true;

    protected function emptyForm(): array
    {
        return ['employee_id' => '', 'project_id' => '', 'work_date' => today()->toDateString(), 'hours' => '', 'description' => ''];
    }

    protected function toForm(Model $t): array
    {
        return [
            'employee_id' => (string) $t->employee_id,
            'project_id' => (string) $t->project_id,
            'work_date' => $t->work_date->toDateString(),
            'hours' => (float) $t->hours,
            'description' => $t->description ?? '',
        ];
    }

    protected function extra(Request $request, array $l): array
    {
        // Utilization panel has its own range selector (?days=), kept out of the list filters.
        $days = in_array((int) $request->query('days'), [7, 30, 90], true) ? (int) $request->query('days') : 30;
        $utilRequest = Request::create('/', 'GET', ['days' => $days]);
        $utilization = $this->api()->utilization($utilRequest)->getData(true);

        return [
            'days' => $days,
            'utilization' => $utilization,
            'fields' => [
                ['name' => 'employee_id', 'label' => 'Employee', 'type' => 'select', 'required' => true, 'placeholder' => 'Select...', 'options' => Lookups::activeEmployeeOptions($l)],
                ['name' => 'project_id', 'label' => 'Project', 'type' => 'select', 'required' => true, 'placeholder' => 'Select...', 'options' => Lookups::projectOptions($l, true)],
                ['name' => 'work_date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                ['name' => 'hours', 'label' => 'Hours', 'type' => 'number', 'required' => true, 'step' => '0.25'],
                ['name' => 'description', 'label' => 'What was done', 'full' => true, 'placeholder' => 'e.g. Built checkout page'],
            ],
        ];
    }
}
