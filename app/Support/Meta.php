<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;

/**
 * Label/colour metadata and formatting helpers shared by every admin page.
 * Colour scale: gray = neutral, amber = in flight, green = good, red = needs attention.
 */
class Meta
{
    const GRAY = '#898781';
    const AMBER = '#fab219';
    const BLUE = '#4F46E5';
    const TEAL = '#0E7490';
    const GREEN = '#0ca30c';
    const RED = '#d03b3b';
    const PURPLE = '#7C3AED';

    const LEAD_STATUS = [
        'new' => ['label' => 'New', 'color' => '#898781'],
        'contacted' => ['label' => 'Contacted', 'color' => '#fab219'],
        'proposal' => ['label' => 'Proposal sent', 'color' => '#4F46E5'],
        'negotiation' => ['label' => 'Negotiation', 'color' => '#7C3AED'],
        'won' => ['label' => 'Won', 'color' => '#0ca30c'],
        'lost' => ['label' => 'Lost', 'color' => '#d03b3b'],
    ];

    const LOST_REASONS = ['Price too high', 'Went with a competitor', 'No budget', 'No response', 'Timeline did not fit', 'Not a fit for our services', 'Other'];

    const PROJECT_STATUS = [
        'planning' => ['label' => 'Planning', 'color' => self::GRAY],
        'in_progress' => ['label' => 'In progress', 'color' => self::BLUE],
        'review' => ['label' => 'Review', 'color' => self::AMBER],
        'delivered' => ['label' => 'Delivered', 'color' => self::GREEN],
        'maintenance' => ['label' => 'Maintenance', 'color' => self::TEAL],
        'on_hold' => ['label' => 'On hold', 'color' => self::RED],
    ];

    const INVOICE_STATUS = [
        'draft' => ['label' => 'Draft', 'color' => self::GRAY],
        'sent' => ['label' => 'Sent', 'color' => self::BLUE],
        'partially_paid' => ['label' => 'Partially paid', 'color' => self::AMBER],
        'paid' => ['label' => 'Paid', 'color' => self::GREEN],
        'overdue' => ['label' => 'Overdue', 'color' => self::RED],
        'cancelled' => ['label' => 'Cancelled', 'color' => self::GRAY],
    ];

    const QUOTATION_STATUS = [
        'draft' => ['label' => 'Draft', 'color' => self::GRAY],
        'sent' => ['label' => 'Sent', 'color' => self::BLUE],
        'accepted' => ['label' => 'Accepted', 'color' => self::GREEN],
        'rejected' => ['label' => 'Rejected', 'color' => self::RED],
    ];

    const TICKET_STATUS = [
        'open' => ['label' => 'Open', 'color' => self::BLUE],
        'in_progress' => ['label' => 'In progress', 'color' => self::AMBER],
        'waiting' => ['label' => 'Waiting on client', 'color' => self::PURPLE],
        'resolved' => ['label' => 'Resolved', 'color' => self::GREEN],
        'closed' => ['label' => 'Closed', 'color' => self::GRAY],
    ];

    const TICKET_PRIORITY = [
        'low' => ['label' => 'Low', 'color' => self::GRAY],
        'medium' => ['label' => 'Medium', 'color' => self::BLUE],
        'high' => ['label' => 'High', 'color' => self::AMBER],
        'urgent' => ['label' => 'Urgent', 'color' => self::RED],
    ];

    const RENEWAL_TYPE = [
        'domain' => ['label' => 'Domain', 'color' => self::BLUE],
        'hosting' => ['label' => 'Hosting', 'color' => self::TEAL],
        'ssl' => ['label' => 'SSL', 'color' => self::GREEN],
        'amc' => ['label' => 'AMC', 'color' => self::PURPLE],
        'other' => ['label' => 'Other', 'color' => self::GRAY],
    ];

    const ACTIVE_STATUS = [
        'active' => ['label' => 'Active', 'color' => self::GREEN],
        'inactive' => ['label' => 'Inactive', 'color' => self::GRAY],
    ];

    const PUBLISHED = [
        1 => ['label' => 'Published', 'color' => '#0ca30c'],
        0 => ['label' => 'Draft', 'color' => '#898781'],
    ];

    const AUDIT_ACTIONS = [
        'created' => ['label' => 'Created', 'color' => '#0ca30c'],
        'updated' => ['label' => 'Updated', 'color' => '#4F46E5'],
        'deleted' => ['label' => 'Deleted', 'color' => '#d03b3b'],
    ];

    const AUDIT_MODELS = ['Contact', 'Client', 'Project', 'Quotation', 'Invoice', 'Payment', 'FinanceEntry', 'Renewal', 'Employee', 'Timesheet', 'Ticket', 'Article', 'User'];

    const ACTIVITY_TYPE = [
        'note' => ['label' => 'Note', 'icon' => '📝'],
        'call' => ['label' => 'Call', 'icon' => '📞'],
        'email' => ['label' => 'Email', 'icon' => '✉️'],
        'meeting' => ['label' => 'Meeting', 'icon' => '🤝'],
    ];

    const BILLING_CYCLES = ['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'];

    const PAYMENT_METHODS = ['Bank transfer', 'UPI', 'Card', 'Cash', 'Cheque', 'PayPal', 'Other'];

    const CATEGORY_META = [
        'domain' => ['label' => 'Domain', 'color' => '#4F46E5'],
        'hosting' => ['label' => 'Hosting', 'color' => '#06B6D4'],
        'salary' => ['label' => 'Salary', 'color' => '#7C3AED'],
        'sale' => ['label' => 'Sale / Income', 'color' => '#0ca30c'],
        'other' => ['label' => 'Other', 'color' => '#898781'],
    ];

    const RECURRENCE_OPTIONS = ['' => 'One-off', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'];

    const CURRENCIES = ['INR', 'USD'];

    const ROLE_META = [
        'admin' => ['label' => 'Admin', 'color' => '#4F46E5'],
        'sales' => ['label' => 'Sales', 'color' => '#0E7490'],
        'finance' => ['label' => 'Finance', 'color' => '#0ca30c'],
        'developer' => ['label' => 'Developer', 'color' => '#7C3AED'],
    ];

    const AREA_LABELS = [
        'leads' => 'Leads',
        'clients' => 'Clients',
        'quotations' => 'Quotations',
        'projects' => 'Projects',
        'invoices' => 'Invoices',
        'finances' => 'Finances',
        'renewals' => 'Renewals',
        'employees' => 'Team',
        'timesheets' => 'Timesheets',
        'tickets' => 'Tickets',
        'content' => 'News',
    ];

    /** Seller details printed on invoices and quotations; blank values are left off. */
    const COMPANY = [
        'name' => 'Web4rtTech',
        'tagline' => 'Build · Grow · Automate',
        'email' => 'info@web4rttech.com',
        'phone' => '+91 7417120232',
        'website' => 'web4rttech.com',
        'address' => '',
        'gstin' => '',
        'bank' => [
            'accountName' => '',
            'accountNumber' => '',
            'ifsc' => '',
            'bankName' => '',
            'upi' => '',
        ],
    ];

    public static function options(array $meta): array
    {
        return array_map(fn ($m) => is_array($m) ? $m['label'] : $m, $meta);
    }

    public static function areasFor(?string $role): string
    {
        if ($role === 'admin') {
            return 'Everything, including users and the audit log';
        }

        $areas = [];
        foreach (User::AREA_ROLES as $area => $roles) {
            if (in_array($role, $roles, true) && isset(self::AREA_LABELS[$area])) {
                $areas[] = self::AREA_LABELS[$area];
            }
        }

        return implode(', ', $areas);
    }

    public static function symbol(?string $currency): string
    {
        return $currency === 'USD' ? '$' : '₹';
    }

    /** "₹1,23,457": rounded, Indian digit grouping. */
    public static function money($amount, ?string $currency = 'INR'): string
    {
        $n = (int) round((float) $amount);

        return self::symbol($currency).($n < 0 ? '-' : '').self::indian(abs($n));
    }

    /** "₹1,23,456.78": two decimals, Indian digit grouping (used on printouts). */
    public static function money2($amount, ?string $currency = 'INR'): string
    {
        $value = round((float) $amount, 2);
        $whole = (int) floor(abs($value));
        $fraction = str_pad((string) round((abs($value) - $whole) * 100), 2, '0', STR_PAD_LEFT);
        if ($fraction === '100') {
            $whole++;
            $fraction = '00';
        }

        return self::symbol($currency).($value < 0 ? '-' : '').self::indian($whole).'.'.$fraction;
    }

    public static function indian(int $n): string
    {
        $s = (string) $n;
        if (strlen($s) <= 3) {
            return $s;
        }
        $last3 = substr($s, -3);
        $rest = substr($s, 0, -3);

        return preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest).','.$last3;
    }

    public static function usd($amount): string
    {
        return '$'.number_format(round((float) $amount));
    }

    protected static function carbon($value): ?Carbon
    {
        if (! $value) {
            return null;
        }
        $date = $value instanceof \DateTimeInterface ? Carbon::instance($value) : Carbon::parse($value);

        return $date;
    }

    /** "05 Sep 2026" */
    public static function date($value): string
    {
        $d = self::carbon($value);

        return $d ? $d->format('d M Y') : '—';
    }

    /** Timestamp in the display timezone, e.g. "5/9/2026, 3:45:12 pm". */
    public static function dateTime($value): string
    {
        $d = self::carbon($value);

        return $d ? $d->copy()->setTimezone(config('app.display_timezone'))->format('j/n/Y, g:i:s a') : '—';
    }

    /** "05 Sep, 03:45 pm" */
    public static function shortDateTime($value): string
    {
        $d = self::carbon($value);

        return $d ? $d->copy()->setTimezone(config('app.display_timezone'))->format('d M, h:i a') : '—';
    }

    /** ISO date string (Y-m-d) of a date-ish value, for form inputs. */
    public static function iso($value): string
    {
        $d = self::carbon($value);

        return $d ? $d->toDateString() : '';
    }

    public static function clientLabel($client): string
    {
        if (! $client) {
            return '—';
        }

        return $client->company ?: $client->name;
    }

    /** Numbers without trailing zeros: 2.50 -> "2.5", 3.00 -> "3". */
    public static function num($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
