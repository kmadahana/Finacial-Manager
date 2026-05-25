<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class Index extends Component
{
    public string $period = '6m'; // 6m, 12m, ytd

    public function render()
    {
        $userId = auth()->id();
        $now    = now();

        $months = match ($this->period) {
            '12m'  => 12,
            'ytd'  => max(1, $now->month),
            default => 6,
        };
        $start = $now->copy()->subMonths($months - 1)->startOfMonth();

        // ── Monthly trend (income vs expense) ─────────────────────
        $rows = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$start, $now])
            ->selectRaw("
                strftime('%Y-%m', transaction_date) as ym,
                type,
                SUM(amount) as total
            ")
            ->groupBy('ym', 'type')
            ->get();

        $monthly = [];
        for ($i = 0; $i < $months; $i++) {
            $m  = $start->copy()->addMonths($i);
            $ym = $m->format('Y-m');
            $monthly[$ym] = [
                'label'   => $m->format('M'),
                'income'  => 0,
                'expense' => 0,
            ];
        }
        foreach ($rows as $r) {
            if (isset($monthly[$r->ym])) {
                $monthly[$r->ym][$r->type] = (float) $r->total;
            }
        }
        $monthly = array_values($monthly);
        $maxBar  = max(1, collect($monthly)->flatMap(fn($m) => [$m['income'], $m['expense']])->max());

        // ── Category breakdown (this month, expenses) ─────────────
        $catRows = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($g) => [
                'name'  => $g->first()->category?->name  ?? 'Uncategorised',
                'color' => $g->first()->category?->color ?? '#64748b',
                'icon'  => $g->first()->category?->icon  ?? 'ti-tag',
                'total' => (float) $g->sum('amount'),
                'count' => $g->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $categoryTotal = (float) $catRows->sum('total');

        // ── Totals for the selected period ────────────────────────
        $totals = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$start, $now])
            ->selectRaw("
                SUM(CASE WHEN type='income'  THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense,
                COUNT(*) as count
            ")->first();

        $income  = (float) ($totals->income  ?? 0);
        $expense = (float) ($totals->expense ?? 0);

        return view('livewire.reports.index', [
            'monthly'        => $monthly,
            'maxBar'         => $maxBar,
            'catRows'        => $catRows,
            'categoryTotal'  => $categoryTotal,
            'totalIncome'    => $income,
            'totalExpense'   => $expense,
            'netBalance'     => $income - $expense,
            'totalCount'     => (int) ($totals->count ?? 0),
            'periodLabel'    => $start->format('M Y') . ' – ' . $now->format('M Y'),
            'savingsRate'    => $income > 0 ? round((($income - $expense) / $income) * 100, 1) : 0,
        ])->layout('components.layouts.app');
    }
}
