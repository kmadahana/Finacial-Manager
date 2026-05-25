<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;

class Dashboard extends Component
{
    public function render()
    {
        $userId = auth()->id();
        $now    = now();

        // This month totals
        $thisMonth = Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->selectRaw("
                SUM(CASE WHEN type='income'  THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
            ")->first();

        // Last month totals (for % comparison)
        $lastMonth = Transaction::where('user_id', $userId)
            ->whereYear('transaction_date', $now->copy()->subMonth()->year)
            ->whereMonth('transaction_date', $now->copy()->subMonth()->month)
            ->selectRaw("
                SUM(CASE WHEN type='income'  THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
            ")->first();

        // All-time balance
        $allTime = Transaction::where('user_id', $userId)
            ->selectRaw("
                SUM(CASE WHEN type='income'  THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
            ")->first();

        $income     = (float) ($thisMonth->income      ?? 0);
        $expense    = (float) ($thisMonth->expense     ?? 0);
        $lastInc    = (float) ($lastMonth->income      ?? 0);
        $lastExp    = (float) ($lastMonth->expense     ?? 0);
        $totalBal   = (float) (($allTime->income ?? 0) - ($allTime->expense ?? 0));

        // Recent transactions
        $recent = Transaction::where('user_id', $userId)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Category breakdown — this month
        $categoryRows = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
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
            'totalBalance'  => $totalBal,
            'monthIncome'   => $income,
            'monthExpense'  => $expense,
            'incomeChange'  => $this->pctChange($lastInc, $income),
            'expenseChange' => $this->pctChange($lastExp, $expense),
            'recent'        => $recent,
            'categoryRows'  => $categoryRows,
            'categoryTotal' => $catSum,
        ])->layout('components.layouts.app');
    }

    private function pctChange(float $prev, float $curr): ?float
    {
        if ($prev <= 0) return $curr > 0 ? null : 0; // no baseline → suppress
        return round((($curr - $prev) / $prev) * 100, 1);
    }
}
