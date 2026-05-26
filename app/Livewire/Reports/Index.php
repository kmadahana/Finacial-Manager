<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Transaction;
use App\Support\MonthlyFinance;
use Illuminate\Support\Carbon;

class Index extends Component
{
    public string $period = '6m'; // 6m, 12m, ytd

    public function render()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Everything is measured over PAY CYCLES (not calendar months) and
        // income includes salary — so Reports matches the Dashboard.
        [$cy, $cm] = MonthlyFinance::currentPlanningMonth($user);

        $count = match ($this->period) {
            '12m'  => 12,
            'ytd'  => max(1, $cm),
            default => 6,
        };

        // Build the last N pay-cycles, oldest → current planning cycle.
        // Skip anything before the user registered — no data, no assumed earnings.
        [$ry, $rm] = MonthlyFinance::registrationMonth($user);
        $regFirst  = Carbon::create($ry, $rm, 1);

        $anchor = Carbon::create($cy, $cm, 1);
        $cycles = [];
        for ($i = $count - 1; $i >= 0; $i--) {
            $c = $anchor->copy()->subMonths($i);
            if ($c->lt($regFirst)) {
                continue;
            }
            $cycles[] = MonthlyFinance::for($user, $c->year, $c->month);
        }

        // ── Monthly trend (planned income incl. salary vs expense) ──
        $monthly = array_map(fn (MonthlyFinance $f) => [
            'label'   => Carbon::create($f->year, $f->month, 1)->format('M'),
            'income'  => $f->plannedIncome,
            'expense' => $f->expenses,
        ], $cycles);

        $maxBar = max(1, collect($monthly)->flatMap(fn ($m) => [$m['income'], $m['expense']])->max());

        $totalIncome  = (float) collect($monthly)->sum('income');
        $totalExpense = (float) collect($monthly)->sum('expense');

        // Current planning cycle (last in the list)
        $fin = end($cycles);

        // ── Category breakdown — current cycle window ─────────────
        $catRows = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $fin->windowStart)
            ->where('transaction_date', '<', $fin->windowEnd)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($g) => [
                'name'  => $g->first()->category?->name  ?? 'Uncategorised',
                'color' => $g->first()->category?->color ?? '#64748b',
                'icon'  => $g->first()->category?->icon  ?? 'ti-tag',
                'total' => (float) $g->sum('amount'),
                'count' => $g->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $categoryTotal = (float) $catRows->sum('total');

        // ── Income allocation pie slices — current cycle ──────────
        $plannedIncome = $fin->plannedIncome;
        $pieSlices = [];
        if ($plannedIncome > 0) {
            foreach ($catRows as $row) {
                if ($row['total'] <= 0) continue;
                $pieSlices[] = [
                    'name'   => $row['name'],
                    'color'  => $row['color'],
                    'amount' => $row['total'],
                    'pct'    => round(($row['total'] / $plannedIncome) * 100, 1),
                ];
            }
            $savings = max(0.0, $plannedIncome - $categoryTotal);
            if ($savings > 0) {
                $pieSlices[] = [
                    'name'   => 'Savings',
                    'color'  => '#22c55e',
                    'amount' => $savings,
                    'pct'    => round(($savings / $plannedIncome) * 100, 1),
                ];
            }
        }

        $firstLabel = Carbon::create($cycles[0]->year, $cycles[0]->month, 1)->format('M Y');
        $cycleLabel = Carbon::create($fin->year, $fin->month, 1)->format('M Y');

        return view('livewire.reports.index', [
            'monthly'        => $monthly,
            'maxBar'         => $maxBar,
            'catRows'        => $catRows,
            'categoryTotal'  => $categoryTotal,
            'totalIncome'    => $totalIncome,
            'totalExpense'   => $totalExpense,
            'netBalance'     => $totalIncome - $totalExpense,
            'periodLabel'    => $firstLabel . ' – ' . $cycleLabel,
            'savingsRate'    => $totalIncome > 0 ? round((($totalIncome - $totalExpense) / $totalIncome) * 100, 1) : 0,
            'pieSlices'      => $pieSlices,
            'plannedIncome'  => $plannedIncome,
            'cycleLabel'     => $cycleLabel,
            'windowLabel'    => $fin->windowLabel(),
        ])->layout('components.layouts.app');
    }
}
