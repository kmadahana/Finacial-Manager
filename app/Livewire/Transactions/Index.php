<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Support\MonthlyFinance;
use Illuminate\Support\Carbon;

class Index extends Component
{
    public int $year;
    public int $month;

    public string $qa_amount      = '';
    public ?int   $qa_category_id = null;
    public string $qa_description = '';

    public bool   $showBudgetEditor = false;
    public string $budgetType       = 'expense';
    public array  $budgetEdits      = [];

    public function mount(): void
    {
        [$this->year, $this->month] = MonthlyFinance::currentPlanningMonth(auth()->user());
        Category::seedDefaultsFor(auth()->user());
    }

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
        [$this->year, $this->month] = MonthlyFinance::currentPlanningMonth(auth()->user());
    }

    public function quickAdd(): void
    {
        $data = $this->validate([
            'qa_amount'      => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'qa_category_id' => ['required', 'exists:categories,id'],
            'qa_description' => ['nullable', 'string', 'max:255'],
        ]);

        $cat = Category::where('user_id', auth()->id())
            ->where('id', $data['qa_category_id'])
            ->where('type', 'expense')
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
        session()->flash('qa-success', 'Transaction added.');
    }

    public function deleteTransaction(int $id): void
    {
        Transaction::where('user_id', auth()->id())->where('id', $id)->delete();
    }

    public function openBudgetEditor(string $type = 'expense'): void
    {
        $this->budgetType  = 'expense';
        $this->budgetEdits = [];

        $cats = auth()->user()->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $existing = Budget::where('user_id', auth()->id())
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get()
            ->keyBy('category_id');

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
        session()->flash('success', 'Budget plan saved.');
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

    public function render()
    {
        $userId = auth()->id();

        // Headline figures — shared with the Dashboard via one source of truth.
        // Money is tracked over the pay-cycle window, not the calendar month.
        $fin = MonthlyFinance::for(auth()->user(), $this->year, $this->month);
        $start = $fin->windowStart;
        $end   = $fin->windowEnd; // exclusive

        $categories = auth()->user()->categories()->orderBy('name')->get();

        $budgets = Budget::where('user_id', $userId)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get()
            ->keyBy('category_id');

        $txTotals = Transaction::where('user_id', $userId)
            ->where('transaction_date', '>=', $start)
            ->where('transaction_date', '<', $end)
            ->selectRaw('category_id, type, SUM(amount) as total')
            ->groupBy('category_id', 'type')
            ->get()
            ->keyBy(fn($r) => $r->category_id . '|' . $r->type);

        $expenseRows = $categories->where('type', 'expense')->map(function ($cat) use ($budgets, $txTotals) {
            $budget = (float) ($budgets->get($cat->id)?->planned_amount ?? 0);
            $spent  = (float) ($txTotals->get($cat->id . '|expense')?->total ?? 0);
            $pct    = $budget > 0
                ? round(($spent / $budget) * 100)
                : ($spent > 0 ? 100 : 0);

            $status = match (true) {
                $budget <= 0 => 'none',
                $pct >= 100  => 'over',
                $pct >= 80   => 'warning',
                default      => 'ok',
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

        $recent = Transaction::where('user_id', $userId)
            ->where('transaction_date', '>=', $start)
            ->where('transaction_date', '<', $end)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('livewire.transactions.index', [
            'monthLabel'       => Carbon::create($this->year, $this->month, 1)->format('F Y'),
            'windowLabel'      => $fin->windowLabel(),
            'isCurrentMonth'   => $fin->isCurrentMonth,
            'isFutureMonth'    => $fin->isFutureMonth,
            'daysInMonth'      => $fin->daysInMonth,
            'dayOfMonth'       => $fin->dayOfMonth,
            'monthProgress'    => $fin->monthProgress,
            'salaryAmount'     => $fin->salary,
            'transactionIncome'=> $fin->incomeEntries,
            'plannedIncome'    => $fin->plannedIncome,
            'budgetSet'        => $fin->budgetSet,
            'spent'            => $fin->expenses,
            'remaining'        => $fin->remaining,
            'spentPct'         => $fin->spentPct,
            'expectedByNow'    => $fin->expectedByNow,
            'expenseRows'      => $expenseRows,
            'allCategories'    => $categories->where('type', 'expense')->values(),
            'recent'           => $recent,
            'editorCategories' => $this->showBudgetEditor
                ? $categories->where('type', 'expense')->values()
                : collect(),
        ])->layout('components.layouts.app');
    }
}
