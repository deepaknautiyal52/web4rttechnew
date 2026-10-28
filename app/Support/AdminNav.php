<?php

namespace App\Support;

/**
 * The admin sidebar menu. Also gives each page header the icon of its menu item.
 */
class AdminNav
{
    const SECTIONS = [
        ['items' => [['url' => '/admin', 'label' => 'Overview', 'icon' => 'grid', 'end' => true]]],
        ['heading' => 'Sales', 'items' => [
            ['url' => '/admin/submissions', 'label' => 'Leads', 'icon' => 'inbox', 'area' => 'leads'],
            ['url' => '/admin/quotations', 'label' => 'Quotations', 'icon' => 'file', 'area' => 'quotations'],
            ['url' => '/admin/clients', 'label' => 'Clients', 'icon' => 'building', 'area' => 'clients'],
        ]],
        ['heading' => 'Delivery', 'items' => [
            ['url' => '/admin/projects', 'label' => 'Projects', 'icon' => 'briefcase', 'area' => 'projects'],
            ['url' => '/admin/timesheets', 'label' => 'Timesheets', 'icon' => 'clock', 'area' => 'timesheets'],
            ['url' => '/admin/tickets', 'label' => 'Support Tickets', 'icon' => 'lifebuoy', 'area' => 'tickets'],
        ]],
        ['heading' => 'Money', 'items' => [
            ['url' => '/admin/invoices', 'label' => 'Invoices', 'icon' => 'receipt', 'area' => 'invoices'],
            ['url' => '/admin/finances', 'label' => 'Finances', 'icon' => 'wallet', 'area' => 'finances'],
            ['url' => '/admin/renewals', 'label' => 'Renewals', 'icon' => 'refresh', 'area' => 'renewals'],
        ]],
        ['heading' => 'Company', 'items' => [
            ['url' => '/admin/team', 'label' => 'Team', 'icon' => 'users', 'area' => 'employees'],
            ['url' => '/admin/news', 'label' => 'News', 'icon' => 'news', 'area' => 'content'],
            ['url' => '/admin/users', 'label' => 'Users & Roles', 'icon' => 'shield', 'area' => 'users'],
            ['url' => '/admin/audit-log', 'label' => 'Audit Log', 'icon' => 'list', 'area' => 'audit'],
            ['url' => '/admin/settings', 'label' => 'Settings', 'icon' => 'settings'],
        ]],
    ];

    public static function isActive(array $item): bool
    {
        $path = ltrim($item['url'], '/');

        return !empty($item['end']) ? request()->is($path) : request()->is($path, $path.'/*');
    }

    /** An admin icon as HTML, for row buttons built as strings. */
    public static function icon(string $name): string
    {
        return view('admin.partials.icon', ['name' => $name, 'class' => ''])->render();
    }

    /** Icon name of the menu item for the current page. */
    public static function currentIcon(): string
    {
        foreach (self::SECTIONS as $section) {
            foreach ($section['items'] as $item) {
                if (self::isActive($item)) {
                    return $item['icon'];
                }
            }
        }

        return 'grid';
    }
}
