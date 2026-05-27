<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Support\MonthlyFinance;
use App\Support\CycleLedger;
use Illuminate\Support\Carbon;

#[\Livewire\Attributes\Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Active pay-cycle planning month (e.g. on/after the 25th this is next month)
        [$py, $pm] = MonthlyFinance::currentPlanningMonth($user);
        $fin = MonthlyFinance::for($user, $py, $pm);

        // Previous planning month — for the % comparison badges
        $prevAnchor = Carbon::create($py, $pm, 1)->subMonth();
        $prevFin    = MonthlyFinance::for($user, $prevAnchor->year, $prevAnchor->month);

        $income        = $fin->incomeEntries;
        $expense       = $fin->expenses;
        $plannedIncome = $fin->plannedIncome;

        // Carry-forward: balance accumulates across cycles instead of resetting.
        $carriedIn = CycleLedger::openingBalanceFor($user, $py, $pm);
        $available = $carriedIn + $fin->totalBalance; // carried over + this cycle's net

        // Recent transactions (not cycle-scoped — just the latest activity)
        $recent = Transaction::where('user_id', $userId)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Category breakdown — current pay-cycle window
        $categoryRows = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $fin->windowStart)
            ->where('transaction_date', '<', $fin->windowEnd)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($g) => [
                'name'  => $g->first()->category?->name  ?? 'Other',
                'color' => $g->first()->category?->color ?? '#64748b',
                'total' => (float) $g->sum('amount'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $catSum = (float) $categoryRows->sum('total');

        return view('livewire.dashboard', [
            'totalBalance'   => $available,
            'carriedIn'      => $carriedIn,
            'monthIncome'    => $income,
            'plannedIncome'  => $plannedIncome,
            'monthExpense'   => $expense,
            'projectedSaving'=> $fin->projectedSaving,
            'budgetSet'      => $fin->budgetSet,
            'onTrack'        => $fin->onTrack,
            'planningLabel'  => Carbon::create($py, $pm, 1)->format('F Y'),
            'windowLabel'    => $fin->windowLabel(),
            'incomeChange'   => $this->pctChange($prevFin->incomeEntries, $income),
            'expenseChange'  => $this->pctChange($prevFin->expenses, $expense),
            'recent'         => $recent,
            'categoryRows'   => $categoryRows,
            'categoryTotal'  => $catSum,
        ])->layout('components.layouts.app');
    }

    private function pctChange(float $prev, float $curr): ?float
    {
        if ($prev <= 0) return $curr > 0 ? null : 0;
        return round((($curr - $prev) / $prev) * 100, 1);
    }
}
