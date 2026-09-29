<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\UserController;
use App\Support\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UsersController extends ResourcePageController
{
    protected string $api = UserController::class;

    protected string $view = 'admin.users';

    protected function emptyForm(): array
    {
        return ['name' => '', 'email' => '', 'role' => 'sales', 'password' => ''];
    }

    protected function toForm(Model $u): array
    {
        return ['name' => $u->name, 'email' => $u->email, 'role' => $u->role ?: 'admin', 'password' => ''];
    }

    protected function extra(Request $request, array $lookups): array
    {
        return ['fields' => [
            ['name' => 'name', 'label' => 'Name', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => Meta::options(Meta::ROLE_META)],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'placeholder' => 'At least 8 characters', 'help' => 'Leave blank when editing to keep the current password.'],
        ]];
    }
}
