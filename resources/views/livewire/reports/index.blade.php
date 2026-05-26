<div>
<x-layouts.dashboard title="Reports" subtitle="{{ $periodLabel }}">
    <x-slot name="actions">
        <flux:select wire:model.live="period" size="sm">
            <flux:select.option value="6m">Last 6 months</flux:select.option>
            <flux:select.option value="12m">Last 12 months</flux:select.option>
            <flux:select.option value="ytd">Year to date</flux:select.option>
        </flux:select>
    </x-slot>

    {{-- Summary --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Total income</flux:text>
            <div class="text-xl font-semibold text-green-500">KSh {{ number_format($totalIncome, 0) }}</div>
        </flux:card>
        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Total expenses</flux:text>
            <div class="text-xl font-semibold text-red-500">KSh {{ number_format($totalExpense, 0) }}</div>
        </flux:card>
        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Net balance</flux:text>
            <div class="text-xl font-semibold {{ $netBalance >= 0 ? 'text-green-500' : 'text-red-500' }}">
                KSh {{ number_format($netBalance, 0) }}
            </div>
        </flux:card>
        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Savings rate</flux:text>
            <div class="text-xl font-semibold {{ $savingsRate >= 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $savingsRate }}%
            </div>
        </flux:card>
    </div>

    {{-- Monthly trend chart --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="sm">Income vs Expenses</flux:heading>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-green-500"></span>
                    <span class="text-slate-500 dark:text-slate-400">Income</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-red-500"></span>
                    <span class="text-slate-500 dark:text-slate-400">Expenses</span>
                </span>
            </div>
        </div>

        @if($totalIncome == 0 && $totalExpense == 0)
            <div class="py-12 text-center">
                <flux:text size="sm" class="text-slate-400">
                    No data for this period. Start adding transactions to see trends.
                </flux:text>
            </div>
        @else
            <div class="flex items-end gap-3 h-56 px-2">
                @foreach($monthly as $m)
                    @php
                        $ih = max(2, ($m['income']  / $maxBar) * 100);
                        $eh = max(2, ($m['expense'] / $maxBar) * 100);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
                        <div class="flex items-end gap-1 w-full h-44 justify-center">
                            <div class="w-3 sm:w-4 bg-green-500/80 dark:bg-green-500 rounded-t transition-all duration-700"
                                 style="height: {{ $ih }}%"
                                 title="Income KSh {{ number_format($m['income'], 0) }}"></div>
                            <div class="w-3 sm:w-4 bg-red-500/80 dark:bg-red-500 rounded-t transition-all duration-700"
                                 style="height: {{ $eh }}%"
                                 title="Expense KSh {{ number_format($m['expense'], 0) }}"></div>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate w-full text-center">
                            {{ $m['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </flux:card>

    {{-- Category breakdown --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="sm">Spending by category — {{ $cycleLabel }} cycle</flux:heading>
            <flux:text size="sm" class="text-slate-400">
                Total: KSh {{ number_format($categoryTotal, 2) }}
            </flux:text>
        </div>

        @if($catRows->isEmpty())
            <div class="py-12 text-center">
                <flux:text size="sm" class="text-slate-400">
                    No expenses this month yet.
                </flux:text>
            </div>
        @else
            <div class="space-y-3">
                @foreach($catRows as $row)
                    @php $pct = $categoryTotal > 0 ? round(($row['total'] / $categoryTotal) * 100, 1) : 0; @endphp
                    <div>
                        <div class="flex items-center gap-3 mb-1.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 style="background:{{ $row['color'] }}20">
                                <i class="ti {{ $row['icon'] }} text-sm" style="color:{{ $row['color'] }}"></i>
                            </div>
                            <div class="flex-1 flex justify-between items-center min-w-0">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                        {{ $row['name'] }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $row['count'] }} {{ Str::plural('transaction', $row['count']) }}
                                    </div>
                                </div>
                                <div class="text-right ml-2">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                        KSh {{ number_format($row['total'], 2) }}
                                    </div>
                                    <div class="text-xs text-slate-400">{{ $pct }}%</div>
                                </div>
                            </div>
                        </div>
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden ml-11">
                            <div class="h-1.5 rounded-full transition-all duration-700"
                                 style="width:{{ $pct }}%; background:{{ $row['color'] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </flux:card>

    {{-- Income Allocation donut chart --}}
    <flux:card>
        <div class="flex items-center justify-between mb-2">
            <flux:heading size="sm">Income Allocation — {{ $cycleLabel }}</flux:heading>
        </div>
        <flux:text size="sm" class="text-slate-400 mb-6">Where your planned income goes this cycle ({{ $windowLabel }})</flux:text>

        @if($plannedIncome <= 0 || empty($pieSlices))
            <div class="py-12 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <i class="ti ti-chart-donut text-xl text-slate-400"></i>
                </div>
                <flux:text size="sm" class="text-slate-400">
                    Set your salary and add expenses to see your income allocation.
                </flux:text>
            </div>
        @else
            <div class="flex flex-col sm:flex-row items-center gap-8">

                {{-- Donut SVG --}}
                <div class="relative shrink-0" style="width:160px; height:160px;">
                    <svg viewBox="0 0 42 42" width="160" height="160" class="rotate-[-90deg]">
                        @php
                            $cumOffset = 0;
                        @endphp
                        @foreach($pieSlices as $slice)
                            @php
                                $dashArray  = $slice['pct'] . ' ' . (100 - $slice['pct']);
                                $dashOffset = 100 - $cumOffset;
                                $cumOffset += $slice['pct'];
                            @endphp
                            <circle
                                cx="21" cy="21" r="15.9155"
                                fill="transparent"
                                stroke="{{ $slice['color'] }}"
                                stroke-width="6"
                                stroke-dasharray="{{ $dashArray }}"
                                stroke-dashoffset="{{ $dashOffset }}"
                            />
                        @endforeach
                    </svg>
                    {{-- Center label --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                        <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                            {{ number_format($plannedIncome, 0) }}
                        </div>
                        <div class="text-[10px] text-slate-400 leading-tight">Planned</div>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex-1 w-full space-y-2.5">
                    @foreach($pieSlices as $slice)
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background:{{ $slice['color'] }};"></span>
                            <span class="flex-1 text-sm text-slate-700 dark:text-slate-300 truncate">
                                {{ $slice['name'] }}
                            </span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                                KSh {{ number_format($slice['amount'], 0) }}
                            </span>
                            <span class="text-xs text-slate-400 w-10 text-right whitespace-nowrap">
                                {{ $slice['pct'] }}%
                            </span>
                        </div>
                    @endforeach
                </div>

            </div>
        @endif
    </flux:card>

</x-layouts.dashboard>
</div>
