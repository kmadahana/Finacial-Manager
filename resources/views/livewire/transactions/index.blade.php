<div>
<x-layouts.dashboard title="Budget" subtitle="Plan the month, watch it deplete">

    {{-- Success flashes --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">{{ session('success') }}</flux:callout>
    @endif

    {{-- ───────────────────────── MONTH SELECTOR ───────────────────────── --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <flux:button variant="subtle" square wire:click="prevMonth" aria-label="Previous month">
            <flux:icon.chevron-left variant="mini" />
        </flux:button>

        <div class="min-w-[12rem] text-center">
            <div class="text-xl font-semibold text-slate-900 dark:text-white">
                {{ $monthLabel }}
            </div>
            <div class="text-xs text-slate-400">Pay cycle: {{ $windowLabel }}</div>
            @if(!$isCurrentMonth)
                <flux:link wire:click="goToCurrentMonth" class="text-xs cursor-pointer">
                    Jump to current cycle
                </flux:link>
            @endif
        </div>

        <flux:button variant="subtle" square wire:click="nextMonth" aria-label="Next month">
            <flux:icon.chevron-right variant="mini" />
        </flux:button>
    </div>

    {{-- ───────────────────────── METRIC CARDS ───────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-1">Planned income</flux:text>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white mb-1">
                KSh {{ number_format($plannedIncome, 0) }}
            </div>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400">
                Salary KSh {{ number_format($salaryAmount, 0) }} + KSh {{ number_format($transactionIncome, 0) }} entries
            </flux:text>
        </flux:card>

        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-1">Budget set</flux:text>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white mb-1">
                KSh {{ number_format($budgetSet, 0) }}
            </div>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400">
                KSh {{ number_format($spent, 0) }} spent
            </flux:text>
        </flux:card>

        <flux:card>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-1">Remaining to spend</flux:text>
            <div class="text-2xl font-semibold mb-1 {{ $remaining >= 0 ? 'text-green-500' : 'text-red-500' }}">
                KSh {{ number_format($remaining, 0) }}
            </div>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400">
                @if($budgetSet <= 0)
                    Set a budget to track this
                @elseif($remaining < 0)
                    Over budget by KSh {{ number_format(abs($remaining), 0) }}
                @else
                    of KSh {{ number_format($budgetSet, 0) }} budget
                @endif
            </flux:text>
        </flux:card>

    </div>

    {{-- ───────────────────────── MONTH PROGRESS BAR ───────────────────────── --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <flux:heading size="sm">Month progress</flux:heading>
            <flux:text size="sm" class="text-slate-500 dark:text-slate-400">
                Day {{ $dayOfMonth }} of {{ $daysInMonth }} · {{ number_format($monthProgress, 0) }}% through cycle
            </flux:text>
        </div>

        @php
            $barColor = match(true) {
                $spentPct >= 100 => '#ef4444',
                $spentPct >= 80  => '#f59e0b',
                default          => '#22c55e',
            };
        @endphp

        <div class="relative h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-visible">
            <div class="absolute inset-y-0 left-0 rounded-full transition-all duration-700"
                 style="width: {{ $spentPct }}%; background: {{ $barColor }};"></div>

            @if($monthProgress > 0 && $monthProgress < 100)
                <div class="absolute -top-1 -bottom-1 w-0.5 bg-slate-900 dark:bg-white"
                     style="left: {{ $monthProgress }}%;"
                     title="Today"></div>
                <div class="absolute -top-5 text-[10px] text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap"
                     style="left: calc({{ $monthProgress }}% - 12px);">today</div>
            @endif
        </div>

        <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mt-3">
            <span>
                @if($budgetSet > 0)
                    <span class="font-medium text-slate-900 dark:text-white">{{ number_format($spentPct, 0) }}%</span> of budget spent
                @else
                    No budget yet — click <span class="font-medium">Edit budgets</span> below
                @endif
            </span>
            @if($budgetSet > 0 && $expectedByNow > 0)
                <span>
                    @if($spent <= $expectedByNow)
                        KSh {{ number_format($expectedByNow - $spent, 0) }} under pace
                    @else
                        KSh {{ number_format($spent - $expectedByNow, 0) }} over pace
                    @endif
                </span>
            @endif
        </div>
    </flux:card>

    {{-- ───────────────────────── BUDGET CATEGORIES (full width) ───────────────────────── --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="sm" class="flex items-center gap-2">
                <i class="ti ti-trending-down text-red-500"></i> Budget categories
            </flux:heading>
            <flux:button size="xs" variant="ghost" icon="adjustments-horizontal"
                         wire:click="openBudgetEditor('expense')">
                Edit budgets
            </flux:button>
        </div>

        @if($expenseRows->isEmpty())
            <flux:text size="sm" class="text-center py-8 text-slate-400">
                No expense categories yet — add some in
                <flux:link href="{{ route('categories') }}">Categories</flux:link>.
            </flux:text>
        @else
            <div class="space-y-4">
                @foreach($expenseRows as $row)
                    @php
                        $barColor = match($row->status) {
                            'over'    => '#ef4444',
                            'warning' => '#f59e0b',
                            'ok'      => '#22c55e',
                            default   => '#94a3b8',
                        };
                        $textColor = match($row->status) {
                            'over'    => 'text-red-500',
                            'warning' => 'text-amber-500',
                            default   => 'text-slate-900 dark:text-white',
                        };
                    @endphp
                    <div wire:key="exp-row-{{ $row->category->id }}">
                        <div class="flex items-center gap-3 mb-1.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 style="background: {{ $row->category->color }}20;">
                                <i class="ti {{ $row->category->icon }} text-sm"
                                   style="color: {{ $row->category->color }};"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                    {{ $row->category->name }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold {{ $textColor }}">
                                    KSh {{ number_format($row->spent, 0) }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    @if($row->budget > 0)
                                        of KSh {{ number_format($row->budget, 0) }}
                                    @else
                                        no budget
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden ml-11 relative">
                            <div class="h-1.5 rounded-full transition-all duration-700"
                                 style="width: {{ $row->pct }}%; background: {{ $barColor }};"></div>
                        </div>
                        @if($row->status === 'over')
                            <div class="ml-11 mt-1 text-xs text-red-500 font-medium">
                                Over by KSh {{ number_format($row->spent - $row->budget, 0) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </flux:card>

    {{-- ───────────────────────── QUICK-ADD + RECENT ───────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Quick add --}}
        <flux:card>
            <flux:heading size="sm" class="mb-4 flex items-center gap-2">
                <i class="ti ti-plus text-red-500"></i> Quick add expense
            </flux:heading>

            @if(session('qa-success'))
                <flux:callout variant="success" icon="check-circle" class="mb-3 py-2">
                    {{ session('qa-success') }}
                </flux:callout>
            @endif

            @if(!$isCurrentMonth)
                <flux:callout variant="warning" icon="information-circle" class="mb-3 py-2">
                    Quick-add always uses today's date — it will land in the current pay cycle, not {{ $monthLabel }}.
                </flux:callout>
            @endif

            <form wire:submit="quickAdd" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label>Amount (KSh)</flux:label>
                        <flux:input wire:model="qa_amount" type="number" step="0.01" min="0.01" placeholder="0.00" />
                        <flux:error name="qa_amount" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Category</flux:label>
                        <flux:select wire:model="qa_category_id" placeholder="Choose…">
                            @foreach($allCategories as $cat)
                                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="qa_category_id" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Note (optional)</flux:label>
                    <flux:input wire:model="qa_description" placeholder="e.g. Lunch at Java" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full">
                    Save transaction
                </flux:button>
            </form>
        </flux:card>

        {{-- Recent activity --}}
        <flux:card>
            <div class="flex items-center justify-between mb-4 gap-2">
                <flux:heading size="sm" class="flex items-center gap-2">
                    <i class="ti ti-history text-slate-500"></i> Recent activity
                </flux:heading>
                <flux:link href="{{ route('transactions.all') }}" class="text-xs">View all →</flux:link>
            </div>

            @if($recent->isEmpty())
                <div class="py-10 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <i class="ti ti-inbox text-xl text-slate-400"></i>
                    </div>
                    <flux:text size="sm" class="text-slate-400">No activity this month yet</flux:text>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($recent as $tx)
                        <div class="flex items-center gap-3 py-2.5" wire:key="recent-{{ $tx->id }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                 style="background: {{ ($tx->category->color ?? '#64748b') }}20;">
                                <i class="ti {{ $tx->category->icon ?? 'ti-tag' }} text-sm"
                                   style="color: {{ $tx->category->color ?? '#64748b' }};"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                    {{ $tx->description ?: ($tx->category->name ?? 'Untitled') }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $tx->category->name ?? 'Uncategorised' }} · {{ $tx->transaction_date->format('M j') }}
                                </div>
                            </div>
                            <div class="text-sm font-semibold whitespace-nowrap {{ $tx->type === 'income' ? 'text-green-500' : 'text-red-500' }}">
                                {{ $tx->type === 'income' ? '+' : '−' }}KSh {{ number_format($tx->amount, 0) }}
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
            @endif
        </flux:card>

    </div>

    {{-- ───────────────────────── BUDGET EDITOR MODAL ───────────────────────── --}}
    @if($showBudgetEditor)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-on:keydown.escape.window="$wire.set('showBudgetEditor', false)">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                 wire:click="$set('showBudgetEditor', false)"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800
                        w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <flux:heading size="sm">Set monthly budgets</flux:heading>
                        <flux:text size="sm">{{ $monthLabel }}</flux:text>
                    </div>
                    <flux:button size="xs" variant="subtle" square
                                 wire:click="$set('showBudgetEditor', false)" aria-label="Close">
                        <flux:icon.x-mark variant="mini" />
                    </flux:button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <div class="flex justify-end mb-3">
                        <flux:button size="xs" variant="ghost" icon="arrow-uturn-left"
                                     wire:click="copyFromPreviousMonth">
                            Copy from last month
                        </flux:button>
                    </div>

                    @if($editorCategories->isEmpty())
                        <flux:text size="sm" class="text-center py-8 text-slate-400">
                            No expense categories yet — add some in
                            <flux:link href="{{ route('categories') }}">Categories</flux:link>.
                        </flux:text>
                    @else
                        <div class="space-y-3">
                            @foreach($editorCategories as $cat)
                                <div class="flex items-center gap-3" wire:key="edit-{{ $cat->id }}">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                         style="background: {{ $cat->color }}20;">
                                        <i class="ti {{ $cat->icon }} text-base"
                                           style="color: {{ $cat->color }};"></i>
                                    </div>
                                    <label class="flex-1 text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $cat->name }}
                                    </label>
                                    <div class="w-36">
                                        <flux:input wire:model="budgetEdits.{{ $cat->id }}"
                                                    type="number" step="0.01" min="0"
                                                    placeholder="0" size="sm" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    <flux:button variant="ghost" wire:click="$set('showBudgetEditor', false)">Cancel</flux:button>
                    <flux:button variant="primary" wire:click="saveBudgets">Save plan</flux:button>
                </div>
            </div>
        </div>
    @endif

</x-layouts.dashboard>
</div>
