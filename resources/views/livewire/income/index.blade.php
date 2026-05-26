<div>
<x-layouts.dashboard title="Income" subtitle="Manage your monthly earnings">

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('income-success'))
        <flux:callout variant="success" icon="check-circle" class="mb-5">
            {{ session('income-success') }}
        </flux:callout>
    @endif
    @if(session('income-error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-5">
            {{ session('income-error') }}
        </flux:callout>
    @endif

    {{-- ── SALARY — full-width card at top ── --}}
    <flux:card class="mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <i class="ti ti-briefcase text-xl leading-none text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <flux:heading size="sm">Monthly Salary</flux:heading>
                    <flux:text size="sm" class="text-slate-400">
                        Constant every month — only change when your pay changes
                    </flux:text>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right">
                    @if($salary > 0)
                        <div class="text-3xl font-extrabold text-green-600 dark:text-green-400">
                            KSh {{ number_format($salary, 0) }}
                        </div>
                        <flux:text size="sm" class="text-slate-400">per month</flux:text>
                    @else
                        <div class="text-xl font-semibold text-slate-400">Not set yet</div>
                        <flux:text size="sm" class="text-slate-400">click to add your salary</flux:text>
                    @endif
                </div>
                <flux:button variant="primary" size="sm" wire:click="openSalaryEdit">
                    {{ $salary > 0 ? 'Edit salary' : 'Set salary' }}
                </flux:button>
            </div>
        </div>
    </flux:card>

    {{-- ── SUMMARY STRIP ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <flux:card>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <i class="ti ti-briefcase text-xs text-green-600 dark:text-green-400"></i>
                </div>
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Salary</flux:text>
            </div>
            <div class="text-2xl font-semibold text-green-600 dark:text-green-400">
                KSh {{ number_format($salary, 0) }}
            </div>
            <flux:text size="sm" class="text-slate-400 mt-1">Monthly base</flux:text>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class="ti ti-coins text-xs text-blue-600 dark:text-blue-400"></i>
                </div>
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Extra — {{ $monthLabel }}</flux:text>
            </div>
            <div class="text-2xl font-semibold text-blue-600 dark:text-blue-400">
                KSh {{ number_format($additionalTotal, 0) }}
            </div>
            <flux:text size="sm" class="text-slate-400 mt-1">Gifts, freelance &amp; other</flux:text>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                    <i class="ti ti-chart-bar text-xs text-purple-600 dark:text-purple-400"></i>
                </div>
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Total planned</flux:text>
            </div>
            <div class="text-2xl font-semibold text-purple-600 dark:text-purple-400">
                KSh {{ number_format($totalPlanned, 0) }}
            </div>
            <flux:text size="sm" class="text-slate-400 mt-1">{{ $monthLabel }}</flux:text>
        </flux:card>
    </div>

    {{-- ── ADDITIONAL INCOME SECTION ── --}}
    <div class="grid lg:grid-cols-5 gap-6">

        {{-- LEFT: Add / Edit entry form ── --}}
        <div class="lg:col-span-2">
            <flux:card class="h-full">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <i class="ti ti-coins text-lg leading-none text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <flux:heading size="sm">
                            {{ $editingEntryId ? 'Edit Entry' : 'Add Extra Income' }}
                        </flux:heading>
                        <flux:text size="sm" class="text-slate-400">Gifts, freelance, investments, etc.</flux:text>
                    </div>
                </div>

                @if($incomeCategories->isEmpty())
                    <flux:callout variant="warning" icon="exclamation-triangle">
                        No income categories available (other than Salary).
                        <a href="{{ route('categories') }}" class="underline ml-1">Add one →</a>
                    </flux:callout>
                @else
                    <form wire:submit="{{ $editingEntryId ? 'updateEntry' : 'saveEntry' }}" class="space-y-4">
                        <flux:field>
                            <flux:label>Category</flux:label>
                            <flux:select wire:model="entryType" placeholder="Choose category…">
                                @foreach($incomeCategories as $cat)
                                    <flux:select.option value="{{ $cat->id }}">
                                        {{ $cat->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="entryType" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Amount (KSh)</flux:label>
                            <flux:input wire:model="entryAmount"
                                        type="number" step="0.01" min="0.01"
                                        placeholder="e.g. 12000" />
                            <flux:error name="entryAmount" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Description <span class="text-slate-400">(optional)</span></flux:label>
                            <flux:input wire:model="entryDescription"
                                        placeholder="e.g. Logo design for client" />
                            <flux:error name="entryDescription" />
                        </flux:field>

                        <div class="flex gap-2">
                            <flux:button type="submit" variant="primary">
                                {{ $editingEntryId ? 'Update entry' : 'Add entry' }}
                            </flux:button>
                            @if($editingEntryId)
                                <flux:button type="button" variant="ghost" wire:click="cancelEditEntry">
                                    Cancel
                                </flux:button>
                            @endif
                        </div>
                    </form>
                @endif
            </flux:card>
        </div>

        {{-- RIGHT: Month nav + Entries list ── --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Month selector — sits right above the entries it controls ── --}}
            <div class="flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-2 py-2 shadow-sm">
                <flux:button variant="subtle" square wire:click="prevMonth" aria-label="Previous month">
                    <flux:icon.chevron-left variant="mini" />
                </flux:button>
                <div class="text-center">
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $monthLabel }}</span>
                    <div class="text-xs text-slate-400 mt-0.5">Showing extra income for this month</div>
                </div>
                <flux:button variant="subtle" square wire:click="nextMonth"
                             :disabled="$isCurrentMonth"
                             aria-label="Next month">
                    <flux:icon.chevron-right variant="mini" />
                </flux:button>
            </div>

            {{-- Entries list ── --}}
            <flux:card>
                <flux:heading size="sm" class="mb-4">
                    Extra income — {{ $monthLabel }}
                </flux:heading>

                @if($entries->isEmpty())
                    <div class="py-10 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <i class="ti ti-coins text-xl text-slate-400"></i>
                        </div>
                        <flux:text size="sm" class="text-slate-400">
                            No extra income recorded for {{ $monthLabel }}.
                        </flux:text>
                        <flux:text size="sm" class="text-slate-400 mt-1">
                            Use the form on the left to add gifts, freelance work, etc.
                        </flux:text>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($entries as $tx)
                            <div class="flex items-center justify-between py-3 gap-3" wire:key="entry-{{ $tx->id }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                         style="background-color: {{ $tx->category?->color ?? '#64748b' }}22;">
                                        <i class="ti {{ $tx->category?->icon ?? 'ti-coins' }} text-base leading-none"
                                           style="color: {{ $tx->category?->color ?? '#64748b' }};"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                            {{ $tx->category?->name ?? 'Income' }}
                                        </div>
                                        @if($tx->description)
                                            <div class="text-xs text-slate-400 truncate">{{ $tx->description }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400 whitespace-nowrap">
                                        +KSh {{ number_format($tx->amount, 0) }}
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <flux:button size="xs" variant="subtle" square
                                                     wire:click="editEntry({{ $tx->id }})"
                                                     aria-label="Edit">
                                            <flux:icon.pencil-square variant="mini" />
                                        </flux:button>
                                        <flux:button size="xs" variant="subtle" square
                                                     wire:click="deleteEntry({{ $tx->id }})"
                                                     wire:confirm="Remove this income entry?"
                                                     aria-label="Delete">
                                            <flux:icon.trash variant="mini" class="text-red-500" />
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <flux:text size="sm" class="text-slate-500 dark:text-slate-400">Total extra</flux:text>
                        <span class="text-base font-extrabold text-green-600 dark:text-green-400">
                            KSh {{ number_format($additionalTotal, 0) }}
                        </span>
                    </div>
                @endif
            </flux:card>

        </div>
    </div>

    {{-- ── SALARY MODAL ── --}}
    @if($editingSalary)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-on:keydown.escape.window="$wire.cancelSalaryEdit()">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                 wire:click="cancelSalaryEdit"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-sm">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <flux:heading size="sm">{{ $salary > 0 ? 'Edit salary' : 'Set salary' }}</flux:heading>
                        <flux:text size="sm" class="text-slate-400">Stays the same every month until you change it</flux:text>
                    </div>
                    <flux:button size="xs" variant="subtle" square wire:click="cancelSalaryEdit" aria-label="Close">
                        <flux:icon.x-mark variant="mini" />
                    </flux:button>
                </div>

                <form wire:submit="saveSalary" class="px-6 py-5 space-y-4">
                    <flux:field>
                        <flux:label>Monthly salary (KSh)</flux:label>
                        <flux:input wire:model="salaryInput"
                                    type="number" step="0.01" min="0"
                                    placeholder="e.g. 85000" />
                        <flux:error name="salaryInput" />
                    </flux:field>

                    <div class="flex gap-2">
                        <flux:button type="submit" variant="primary" class="flex-1">Save salary</flux:button>
                        <flux:button type="button" variant="ghost" wire:click="cancelSalaryEdit">Cancel</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</x-layouts.dashboard>
</div>
