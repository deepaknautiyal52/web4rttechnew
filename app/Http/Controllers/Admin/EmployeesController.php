<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\EmployeeController;
use App\Support\Meta;
use Illuminate\Http\Request;

class EmployeesController extends ResourcePageController
{
    protected string $api = EmployeeController::class;

    protected string $view = 'admin.employees';

    protected function emptyForm(): array
    {
        return ['name' => '', 'designation' => '', 'email' => '', 'phone' => '', 'monthly_salary' => '', 'hourly_cost' => '', 'currency' => 'INR', 'joined_on' => '', 'status' => 'active'];
    }

    protected function extra(Request $request, array $lookups): array
    {
        return ['fields' => [
            ['name' => 'name', 'label' => 'Full name', 'required' => true],
            ['name' => 'designation', 'label' => 'Designation', 'placeholder' => 'e.g. Senior React Developer'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone'],
            ['name' => 'monthly_salary', 'label' => 'Monthly salary', 'type' => 'number'],
            ['name' => 'hourly_cost', 'label' => 'Hourly cost', 'type' => 'number', 'help' => 'Used for project cost from timesheets. Leave blank to use salary ÷ 160.'],
            ['name' => 'currency', 'label' => 'Currency', 'type' => 'select', 'options' => array_combine(Meta::CURRENCIES, Meta::CURRENCIES)],
            ['name' => 'joined_on', 'label' => 'Joined on', 'type' => 'date'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Meta::options(Meta::ACTIVE_STATUS)],
        ]];
    }
}
