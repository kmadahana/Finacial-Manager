<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Support\Carbon;

class Index extends Component
{
    /* ── Currently viewed month ────────────────────────────────── */
    public int $year;
    public int $month;

    /* ── Quick-add form ─────────────────────────────────────────── */
    public string $qa_amount      = '';
    public ?int   $qa_category_id = null;
    public string $qa_description = '';

    /* ── Budget editor modal ────────────────────────────────────── */
    public bool   $showBudgetEditor = false;
    public string $budgetType       = 'expense';
    public array  $budgetEdits      = [];   // [category_id => string amount]

    public function mount(): void
    {
        $this->year  = now()->year;
        $this->month = now()->month;
        Category::seedDefaultsFor(auth()->user());
    }

    /* ── Month navigation ───────────────────────────────────────── */
    public function prevMonth(): void
    {
        $d = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year  = $d->year;
        $this->month = $d->month;
    }

    public function nextMonth(): void
    {
        $d = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year  = $d->year;
        $this->month = $d->month;
    }

    public function goToCurrentMonth(): void
    {
        $this->year  = now()->year;
        $this->month = now()->month;
    }

    /* ── Quick-add ─────────────────────────────────────────────── */
    public function quickAdd(): void
    {
        $data = $this->validate([
            'qa_amount'      => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'qa_category_id' => ['required', 'exists:categories,id'],
            'qa_description' => ['nullable', 'string', 'max:255'],
        ]);

        $cat = Category::where('user_id', auth()->id())
            ->where('id', $data['qa_category_id'])
            ->firstOrFail();

        Transaction::create([
            'user_id'          => auth()->id(),
            'category_id'      => $cat->id,
            'type'             => $cat->type,
            'amount'           => $data['qa_amount'],
            'description'      => $data['qa_description'] ?: null,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->reset(['qa_amount', 'qa_description']);
        // Keep category selected so successive adds of the same kind are fast.
        session()->flash('qa-success', 'Transaction added.');
    }

    public function deleteTransaction(int $id): void
    {
        Transaction::where('user_id', auth()->id())->where('id', $id)->delete();
    }

    /* ── Budget editor ──────────────────────────────────────────── */
    public function openBudgetEditor(string $type): void
    {
        $type = in_array($type, ['income', 'expense'], true) ? $type : 'expense';
        $this->budgetType  = $type;
        $this->budgetEdits = [];

        $cats = auth()->user()->categories()
            ->where('type', $type)->orderBy('name')->get();

        $existing = Budget::where('user_id', auth()->id())
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get()->keyBy('category_id');

        foreach ($cats as $cat) {
            $b = $existing->get($cat->id);
            $this->budgetEdits[$cat->id] = $b ? rtrim(rtrim((string) $b->planned_amount, '0'), '.') : '';
        }

        $this->showBudgetEditor = true;
    }

    public function saveBudgets(): void
    {
        foreach ($this->budgetEdits as $catId => $amount) {
            $amount = is_string($amount) ? trim($amount) : $amount;
            $val    = ($amount === '' || $amount === null) ? 0 : (float) $amount;

            Budget::updateOrCreate(
                [
                    'user_id'     => auth()->id(),
                    'category_id' => (int) $catId,
                    'year'        => $this->year,
                    'month'       => $this->month,
                ],
                ['planned_amount' => $val]
            );
        }

        $this->showBudgetEditor = false;
        session()->flash('success', ucfirst($this->budgetType) . ' plan saved.');
    }

    public function copyFromPreviousMonth(): void
    {
        $prev = Carbon::create($this->year, $this->month, 1)->subMonth();

        $prevBudgets = Budget::where('user_id', auth()->id())
            ->where('year', $prev->year)
            ->where('month', $prev->month)
            ->get()
            ->keyBy('category_id');

        foreach ($this->budgetEdits as $catId => $_) {
            $b = $prevBudgets->get((int) $catId);
            if ($b && (float) $b->planned_amount > 0) {
                $this->budgetEdits[$catId] = rtrim(rtrim((string) $b->planned_amount, '0'), '.');
            }
        }
    }

    /* ── Render ─────────────────────────────────────────────────── */
    public function render()
    {
        $userId = auth()->id();
        $start  = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $end    = $start->copy()->endOfMonth();
        $today  = now()->startOfDay();

        $isCurrentMonth = $today->year === $this->year && $today->month === $this->month;
        $isFutureMonth  = $start->greaterThan($today);
        $isPastMonth    = $end->lessThan($today);

        $daysInMonth = $start->daysInMonth;
        $dayOfMonth = match (true) {
            $isFutureMonth  => 0,
            $isPastMonth    => $daysInMonth,
            default         => $today->day,
        };
        $monthProgressPct = round(($dayOfMonth / $daysInMonth) * 100, 1);

        /* ── Categories, budgets, transactions ─────────────────── */
        $categories = auth()->user()->categories()->orderBy('name')->get();

        $budgets = Budget::where('user_id', $userId)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get()
            ->keyBy('category_id');

        $txTotals = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('category_id, type, SUM(amount) as total')
            ->groupBy('category_id', 'type')
            ->get()
            ->keyBy(fn($r) => $r->category_id . '|' . $r->type);

        /* ── Income source rows ────────────────────────────────── */
        $incomeRows = $categories->where('type', 'income')->map(function ($cat) use ($budgets, $txTotals) {
            $planned  = (float) ($budgets->get($cat->id)?->planned_amount ?? 0);
            $received = (float) ($txTotals->get($cat->id . '|income')?->total ?? 0);
            $pct      = $planned > 0
                ? min(100, round(($received / $planned) * 100))
                : ($received > 0 ? 100 : 0);

            return (object) [
                'category' => $cat,
                'planned'  => $planned,
                'received' => $received,
                'pct'      => $pct,
            ];
        })->values();

        /* ── Budget category rows ──────────────────────────────── */
        $expenseRows = $categories->where('type', 'expense')->map(function ($cat) use ($budgets, $txTotals) {
            $budget = (float) ($budgets->get($cat->id)?->planned_amount ?? 0);
            $spent  = (float) ($txTotals->get($cat->id . '|expense')?->total ?? 0);
            $pct    = $budget > 0
                ? round(($spent / $budget) * 100)
                : ($spent > 0 ? 100 : 0);

            $status = match (true) {
                $budget <= 0      => 'none',           // no budget set
                $pct >= 100       => 'over',           // 🔴
                $pct >= 80        => 'warning',        // 🟡
                default           => 'ok',             // 🟢
            };

            return (object) [
                'category' => $cat,
                'budget'   => $budget,
                'spent'    => $spent,
                'pct'      => min(100, $pct),
                'over_pct' => max(0, $pct - 100),
                'status'   => $status,
            ];
        })->values();

        /* ── Aggregates / metric cards ─────────────────────────── */
        $plannedIncome   = (float) $incomeRows->sum('planned');
        $receivedIncome  = (float) $incomeRows->sum('received');
        $budgetSet       = (float) $expenseRows->sum('budget');
        $spent           = (float) $expenseRows->sum('spent');
        $remaining       = $budgetSet - $spent;
        $projectedSaving = $plannedIncome - $budgetSet;

        // "On track" — at day X of N, expected linear spend is X/N of budget.
        $expectedByNow = $budgetSet > 0 ? ($dayOfMonth / max(1, $daysInMonth)) * $budgetSet : 0;
        $onTrack = $isFutureMonth
            ? true
            : ($budgetSet === 0.0 ? true : $spent <= $expectedByNow);

        /* ── Spend bar status (for month progress) ─────────────── */
        $spentPct = $budgetSet > 0 ? min(100, ($spent / $budgetSet) * 100) : 0;

        /* ── Recent activity ───────────────────────────────────── */
        $recent = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$start, $end])
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('livewire.transactions.index', [
            // Month context
            'monthLabel'      => $start->format('F Y'),
            'isCurrentMonth'  => $isCurrentMonth,
            'isFutureMonth'   => $isFutureMonth,
            'daysInMonth'     => $daysInMonth,
            'dayOfMonth'      => $dayOfMonth,
            'monthProgress'   => $monthProgressPct,

            // Aggregates
            'plannedIncome'   => $plannedIncome,
            'receivedIncome'  => $receivedIncome,
            'budgetSet'       => $budgetSet,
            'spent'           => $spent,
            'remaining'       => $remaining,
            'projectedSaving' => $projectedSaving,
            'spentPct'        => $spentPct,
            'onTrack'         => $onTrack,
            'expectedByNow'   => $expectedByNow,

            // Rows
            'incomeRows'      => $incomeRows,
            'expenseRows'     => $expenseRows,
            'allCategories'   => $categories,
            'recent'          => $recent,

            // Budget editor data
            'editorCategories' => $this->showBudgetEditor
                ? $categories->where('type', $this->budgetType)->values()
                : collect(),
        ])->layout('components.layouts.app');
    }
}
