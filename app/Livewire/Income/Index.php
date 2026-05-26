<?php

namespace App\Livewire\Income;

use App\Models\Category;
use App\Models\Transaction;
use App\Support\MonthlyFinance;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public float $salary = 0;
    public string $salaryInput = '';
    public bool $editingSalary = false;
    public string $selectedMonth = '';

    public ?int $editingEntryId = null;
    public ?int $entryType = null;
    public string $entryAmount = '';
    public string $entryDescription = '';

    public function mount(): void
    {
        [$py, $pm] = MonthlyFinance::currentPlanningMonth(auth()->user());
        $this->selectedMonth = sprintf('%04d-%02d', $py, $pm);
        $this->salary = (float) (auth()->user()->salary ?? 0);
        Category::seedDefaultsFor(auth()->user());
    }

    private function planningMonth(): Carbon
    {
        [$py, $pm] = MonthlyFinance::currentPlanningMonth(auth()->user());
        return Carbon::create($py, $pm, 1);
    }

    public function prevMonth(): void
    {
        $this->selectedMonth = Carbon::createFromFormat('Y-m', $this->selectedMonth)
            ->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $next = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth()->addMonth();
        if ($next->lte($this->planningMonth())) {
            $this->selectedMonth = $next->format('Y-m');
        }
    }

    public function openSalaryEdit(): void
    {
        $this->editingSalary = true;
        $this->salaryInput = $this->salary > 0 ? number_format($this->salary, 2, '.', '') : '';
    }

    public function saveSalary(): void
    {
        $this->validate([
            'salaryInput' => ['required', 'numeric', 'min:0'],
        ], [
            'salaryInput.required' => 'Please enter a salary amount.',
            'salaryInput.numeric'  => 'Salary must be a valid number.',
            'salaryInput.min'      => 'Salary must be 0 or more.',
        ]);

        $user = auth()->user();
        $user->salary = (float) $this->salaryInput;
        $user->save();

        $this->salary = (float) $user->salary;
        $this->editingSalary = false;
        session()->flash('income-success', 'Salary updated.');
    }

    public function cancelSalaryEdit(): void
    {
        $this->editingSalary = false;
        $this->salaryInput = '';
    }

    public function saveEntry(): void
    {
        $incomeCategories = auth()->user()->categories()
            ->where('type', 'income')
            ->whereRaw('LOWER(name) != ?', ['salary'])
            ->pluck('id')
            ->toArray();

        $this->validate([
            'entryType'        => ['required', 'integer', 'in:' . implode(',', $incomeCategories ?: [0])],
            'entryAmount'      => ['required', 'numeric', 'min:0.01'],
            'entryDescription' => ['nullable', 'string', 'max:255'],
        ], [
            'entryType.required' => 'Please select a category.',
            'entryType.in'       => 'Please select a valid income category.',
            'entryAmount.required' => 'Please enter an amount.',
            'entryAmount.numeric'  => 'Amount must be a valid number.',
            'entryAmount.min'      => 'Amount must be at least 0.01.',
        ]);

        $date = Carbon::createFromFormat('Y-m', $this->selectedMonth)->startOfMonth()->toDateString();

        Transaction::create([
            'user_id'          => auth()->id(),
            'category_id'      => $this->entryType,
            'type'             => 'income',
            'amount'           => $this->entryAmount,
            'description'      => $this->entryDescription ?: null,
            'transaction_date' => $date,
        ]);

        $this->entryAmount = '';
        $this->entryDescription = '';
        session()->flash('income-success', 'Income entry added.');
    }

    public function editEntry(int $id): void
    {
        $tx = Transaction::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'income')
            ->firstOrFail();

        $this->editingEntryId   = $tx->id;
        $this->entryType        = $tx->category_id;
        $this->entryAmount      = number_format((float) $tx->amount, 2, '.', '');
        $this->entryDescription = $tx->description ?? '';
    }

    public function cancelEditEntry(): void
    {
        $this->editingEntryId   = null;
        $this->entryType        = null;
        $this->entryAmount      = '';
        $this->entryDescription = '';
    }

    public function updateEntry(): void
    {
        $incomeCategories = auth()->user()->categories()
            ->where('type', 'income')
            ->whereRaw('LOWER(name) != ?', ['salary'])
            ->pluck('id')
            ->toArray();

        $this->validate([
            'entryType'        => ['required', 'integer', 'in:' . implode(',', $incomeCategories ?: [0])],
            'entryAmount'      => ['required', 'numeric', 'min:0.01'],
            'entryDescription' => ['nullable', 'string', 'max:255'],
        ], [
            'entryType.required'   => 'Please select a category.',
            'entryType.in'         => 'Please select a valid income category.',
            'entryAmount.required' => 'Please enter an amount.',
            'entryAmount.numeric'  => 'Amount must be a valid number.',
            'entryAmount.min'      => 'Amount must be at least 0.01.',
        ]);

        Transaction::where('id', $this->editingEntryId)
            ->where('user_id', auth()->id())
            ->update([
                'category_id' => $this->entryType,
                'amount'      => $this->entryAmount,
                'description' => $this->entryDescription ?: null,
            ]);

        $this->cancelEditEntry();
        session()->flash('income-success', 'Income entry updated.');
    }

    public function deleteEntry(int $id): void
    {
        Transaction::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'income')
            ->delete();

        session()->flash('income-success', 'Entry removed.');
    }

    public function render()
    {
        [$year, $month] = explode('-', $this->selectedMonth);

        $incomeCategories = auth()->user()->categories()
            ->where('type', 'income')
            ->whereRaw('LOWER(name) != ?', ['salary'])
            ->orderBy('name')
            ->get();

        $entries = Transaction::where('user_id', auth()->id())
            ->where('type', 'income')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->whereHas('category', fn($q) => $q->whereRaw('LOWER(name) != ?', ['salary']))
            ->with('category')
            ->orderByDesc('id')
            ->get();

        $additionalTotal = (float) $entries->sum('amount');
        $totalPlanned    = $this->salary + $additionalTotal;
        $monthLabel      = Carbon::createFromFormat('Y-m', $this->selectedMonth)->format('F Y');
        $isCurrentMonth  = $this->selectedMonth === $this->planningMonth()->format('Y-m');

        return view('livewire.income.index', compact(
            'incomeCategories',
            'entries',
            'additionalTotal',
            'totalPlanned',
            'monthLabel',
            'isCurrentMonth',
        ))->layout('components.layouts.app');
    }
}
