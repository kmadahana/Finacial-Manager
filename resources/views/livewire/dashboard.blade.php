<div>
<x-layouts.dashboard title="Dashboard" subtitle="{{ now()->format('l, F j Y') }}">
    <x-slot name="actions">
        <flux:badge color="red" class="animate-pulse">Live</flux:badge>
        <flux:button href="{{ route('transactions') }}" variant="primary" icon="plus" size="sm">
            New transaction
        </flux:button>
    </x-slot>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <flux:card class="hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-3">
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Total Balance</flux:text>
                @if($totalBalance >= 0)
                    <flux:badge color="green" size="sm">Positive</flux:badge>
                @else
                    <flux:badge color="red" size="sm">Negative</flux:badge>
                @endif
            </div>
            <div class="text-3xl font-semibold mb-1 {{ $totalBalance >= 0 ? 'text-green-500' : 'text-red-500' }}">
                KSh {{ number_format($totalBalance, 2) }}
            </div>
            <flux:text size="sm" class="text-slate-400">All-time net</flux:text>
        </flux:card>

        <flux:card class="hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-3">
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Monthly Income</flux:text>
                @if(!is_null($incomeChange))
                    <flux:badge color="{{ $incomeChange >= 0 ? 'green' : 'red' }}" size="sm">
                        {{ $incomeChange >= 0 ? '+' : '' }}{{ $incomeChange }}%
                    </flux:badge>
                @endif
            </div>
            <div class="text-3xl font-semibold text-slate-900 dark:text-white mb-1">
                KSh {{ number_format($monthIncome, 2) }}
            </div>
            <flux:text size="sm" class="text-slate-400">vs last month</flux:text>
        </flux:card>

        <flux:card class="hover:-translate-y-0.5 transition-transform">
            <div class="flex justify-between items-start mb-3">
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Monthly Expenses</flux:text>
                @if(!is_null($expenseChange))
                    <flux:badge color="{{ $expenseChange <= 0 ? 'green' : 'red' }}" size="sm">
                        {{ $expenseChange >= 0 ? '+' : '' }}{{ $expenseChange }}%
                    </flux:badge>
                @endif
            </div>
            <div class="text-3xl font-semibold text-red-500 mb-1">
                KSh {{ number_format($monthExpense, 2) }}
            </div>
            <flux:text size="sm" class="text-slate-400">vs last month</flux:text>
        </flux:card>

    </div>

    {{-- Bottom grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Recent transactions --}}
        <flux:card class="lg:col-span-3">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="sm">Recent Transactions</flux:heading>
                <flux:link href="{{ route('transactions') }}" class="text-xs">View all →</flux:link>
            </div>

            @if($recent->isEmpty())
                <div class="py-10 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <i class="ti ti-inbox text-xl text-slate-400"></i>
                    </div>
                    <flux:text size="sm" class="mb-3">No transactions yet</flux:text>
                    <flux:button href="{{ route('transactions') }}" variant="primary" size="sm">
                        Add your first one
                    </flux:button>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($recent as $tx)
                        <div class="flex items-center gap-3 py-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:{{ ($tx->category->color ?? '#64748b') }}20">
                                <i class="ti {{ $tx->category->icon ?? 'ti-tag' }} text-base"
                                   style="color:{{ $tx->category->color ?? '#64748b' }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                    {{ $tx->description ?: ($tx->category->name ?? 'Untitled') }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $tx->category->name ?? 'Uncategorised' }} ·
                                    {{ $tx->transaction_date->format('M j') }}
                                </div>
                            </div>
                            <div class="text-sm font-semibold whitespace-nowrap {{ $tx->type === 'income' ? 'text-green-500' : 'text-red-500' }}">
                                {{ $tx->type === 'income' ? '+' : '−' }}KSh {{ number_format($tx->amount, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

        {{-- Category breakdown --}}
        <flux:card class="lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="sm">Spending by Category</flux:heading>
                <flux:link href="{{ route('reports') }}" class="text-xs">Details →</flux:link>
            </div>

            @if($categoryRows->isEmpty())
                <div class="py-10 text-center">
                    <flux:text size="sm" class="text-slate-400">
                        No expenses this month
                    </flux:text>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($categoryRows as $row)
                        @php $pct = $categoryTotal > 0 ? round(($row['total'] / $categoryTotal) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $row['name'] }}</span>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                    KSh {{ number_format($row['total'], 0) }}
                                </span>
                            </div>
                            <div class="h-1 bg-slate-200 dark:bg-slate-700 rounded-full">
                                <div class="h-1 rounded-full transition-all duration-[1200ms] ease-out"
                                     style="width:{{ $pct }}%; background:{{ $row['color'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

    </div>

</x-layouts.dashboard>
</div>
