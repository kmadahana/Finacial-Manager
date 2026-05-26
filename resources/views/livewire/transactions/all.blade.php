<div>
<x-layouts.dashboard title="All transactions" subtitle="Your complete transaction history">
    <x-slot name="actions">
        <flux:button href="{{ route('transactions') }}" variant="ghost" icon="arrow-left" size="sm">
            Back to budget
        </flux:button>
    </x-slot>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    <flux:card>
        {{-- Filter tabs --}}
        <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
            <flux:heading size="sm" class="flex items-center gap-2">
                <i class="ti ti-history text-slate-500"></i> History
            </flux:heading>

            <flux:radio.group wire:model.live="filter" variant="segmented" size="sm">
                <flux:radio value="all" label="All" />
                <flux:radio value="income" label="Income" />
                <flux:radio value="expense" label="Expense" />
            </flux:radio.group>
        </div>

        @if($transactions->isEmpty())
            <div class="py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <i class="ti ti-inbox text-2xl text-slate-400"></i>
                </div>
                <flux:text size="sm" class="text-slate-400">No transactions to show.</flux:text>
            </div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($transactions as $tx)
                    <div class="flex items-center gap-3 py-3" wire:key="all-tx-{{ $tx->id }}">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background: {{ ($tx->category->color ?? '#64748b') }}20;">
                            <i class="ti {{ $tx->category->icon ?? 'ti-tag' }} text-base"
                               style="color: {{ $tx->category->color ?? '#64748b' }};"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                {{ $tx->description ?: ($tx->category->name ?? 'Untitled') }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $tx->category->name ?? 'Uncategorised' }} ·
                                {{ $tx->transaction_date->format('D, M j Y') }}
                            </div>
                        </div>
                        <div class="text-sm font-semibold whitespace-nowrap {{ $tx->type === 'income' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $tx->type === 'income' ? '+' : '−' }}KSh {{ number_format($tx->amount, 2) }}
                        </div>
                        <flux:button size="xs" variant="subtle" square
                                     wire:click="deleteTransaction({{ $tx->id }})"
                                     wire:confirm="Delete this transaction?"
                                     aria-label="Delete">
                            <flux:icon.trash variant="mini" class="text-red-500" />
                        </flux:button>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </flux:card>

</x-layouts.dashboard>
</div>
