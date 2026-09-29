<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FinanceEntryController;
use App\Models\FinanceEntry;
use Illuminate\Http\Request;

class FinancesController extends Controller
{
    public function index(Request $request)
    {
        $entries = FinanceEntry::query()->orderBy('period_start', 'asc')->get();
        $editing = $request->filled('edit') ? FinanceEntry::findOrFail($request->query('edit')) : null;

        $perMonth = ['monthly' => 1, 'quarterly' => 1 / 3, 'yearly' => 1 / 12];
        $income = $entries->where('type', 'income')->sum(fn ($e) => (float) $e->amount);
        $expense = $entries->where('type', 'expense')->sum(fn ($e) => (float) $e->amount);
        $recurring = $entries->filter(fn ($e) => $e->type === 'expense' && $e->recurrence);

        return view('admin.finances', [
            'entries' => $entries,
            'sorted' => $entries->sortByDesc(fn ($e) => $e->period_start)->values(),
            'editing' => $editing,
            'formOpen' => (bool) ($editing || $request->boolean('create')),
            'stats' => [
                'totalIncome' => $income,
                'totalExpense' => $expense,
                'net' => $income - $expense,
                'monthlyBurn' => $recurring->sum(fn ($e) => (float) $e->amount * ($perMonth[$e->recurrence] ?? 0)),
                'recurringCount' => $recurring->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        app(FinanceEntryController::class)->store($request);

        return redirect()->route('admin.finances.index');
    }

    public function update(Request $request, FinanceEntry $entry)
    {
        app(FinanceEntryController::class)->update($request, $entry);

        return redirect()->route('admin.finances.index');
    }

    public function destroy(FinanceEntry $entry)
    {
        app(FinanceEntryController::class)->destroy($entry);

        return redirect()->route('admin.finances.index');
    }
}
