<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class All extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, income, expense

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function deleteTransaction(int $id): void
    {
        Transaction::where('user_id', auth()->id())->where('id', $id)->delete();
        session()->flash('success', 'Transaction deleted.');
    }

    public function render()
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if (in_array($this->filter, ['income', 'expense'], true)) {
            $query->where('type', $this->filter);
        }

        $transactions = $query->paginate(20);

        return view('livewire.transactions.all', [
            'transactions' => $transactions,
        ])->layout('components.layouts.app');
    }
}
