<div>
<x-layouts.dashboard title="Categories" subtitle="Organise your income and expenses">
    <x-slot name="actions">
        <flux:button variant="primary" icon="plus" wire:click="openCreate">New category</flux:button>
    </x-slot>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- ── FORM (modal-like card) ─────────────────────────────── --}}
    @if($showForm)
        <flux:card class="mb-6 border-red-200 dark:border-red-600/30">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="sm">
                    {{ $editing_id ? 'Edit category' : 'New category' }}
                </flux:heading>
                <flux:button size="xs" variant="subtle" square wire:click="$set('showForm', false)" aria-label="Close">
                    <flux:icon.x-mark variant="mini" />
                </flux:button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Name</flux:label>
                        <flux:input wire:model="name" placeholder="e.g. Groceries" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Type</flux:label>
                        <flux:radio.group wire:model="type" variant="segmented">
                            <flux:radio value="expense" label="Expense" />
                            <flux:radio value="income"  label="Income"  />
                        </flux:radio.group>
                    </flux:field>
                </div>

                {{-- Color picker --}}
                <flux:field>
                    <flux:label>Colour</flux:label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colorOptions as $c)
                            <button type="button" wire:click="$set('color', '{{ $c }}')"
                                    class="w-8 h-8 rounded-lg ring-2 ring-offset-2 ring-offset-white dark:ring-offset-slate-900 transition
                                           {{ $color === $c ? 'ring-slate-900 dark:ring-white' : 'ring-transparent' }}"
                                    style="background:{{ $c }}"
                                    aria-label="{{ $c }}"></button>
                        @endforeach
                    </div>
                </flux:field>

                {{-- Icon picker --}}
                <flux:field>
                    <flux:label>Icon</flux:label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($iconOptions as $i)
                            <button type="button" wire:click="$set('icon', '{{ $i }}')"
                                    class="w-10 h-10 rounded-lg flex items-center justify-center transition
                                           {{ $icon === $i
                                              ? 'bg-red-50 dark:bg-red-600/20 text-red-600 dark:text-red-400 ring-2 ring-red-500'
                                              : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                                <i class="ti {{ $i }} text-lg leading-none"></i>
                            </button>
                        @endforeach
                    </div>
                </flux:field>

                {{-- Preview --}}
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                         style="background:{{ $color }}20">
                        <i class="ti {{ $icon }} text-lg" style="color:{{ $color }}"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $name ?: 'Category name' }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            {{ ucfirst($type) }}
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <flux:button type="submit" variant="primary">
                        {{ $editing_id ? 'Update' : 'Add category' }}
                    </flux:button>
                    <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)">Cancel</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- ── LISTS ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Income --}}
        <flux:card>
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="sm" class="flex items-center gap-2">
                    <i class="ti ti-trending-up text-green-500"></i> Income categories
                </flux:heading>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    {{ $incomeCategories->count() }} {{ Str::plural('category', $incomeCategories->count()) }}
                </span>
            </div>

            @if($incomeCategories->isEmpty())
                <flux:text size="sm" class="text-center py-8 text-slate-400">No income categories yet</flux:text>
            @else
                <div class="space-y-2">
                    @foreach($incomeCategories as $cat)
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                             wire:key="inc-{{ $cat->id }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:{{ $cat->color }}20">
                                <i class="ti {{ $cat->icon }} text-lg" style="color:{{ $cat->color }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $cat->name }}</div>
                                <div class="text-xs text-slate-400">{{ $cat->transactions_count }} {{ Str::plural('transaction', $cat->transactions_count) }}</div>
                            </div>
                            <flux:button size="xs" variant="subtle" square wire:click="edit({{ $cat->id }})">
                                <flux:icon.pencil-square variant="mini" />
                            </flux:button>
                            <flux:button size="xs" variant="subtle" square
                                         wire:click="delete({{ $cat->id }})"
                                         wire:confirm="Delete this category? Transactions will be uncategorised.">
                                <flux:icon.trash variant="mini" class="text-red-500" />
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

        {{-- Expense --}}
        <flux:card>
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="sm" class="flex items-center gap-2">
                    <i class="ti ti-trending-down text-red-500"></i> Expense categories
                </flux:heading>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    {{ $expenseCategories->count() }} {{ Str::plural('category', $expenseCategories->count()) }}
                </span>
            </div>

            @if($expenseCategories->isEmpty())
                <flux:text size="sm" class="text-center py-8 text-slate-400">No expense categories yet</flux:text>
            @else
                <div class="space-y-2">
                    @foreach($expenseCategories as $cat)
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                             wire:key="exp-{{ $cat->id }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background:{{ $cat->color }}20">
                                <i class="ti {{ $cat->icon }} text-lg" style="color:{{ $cat->color }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $cat->name }}</div>
                                <div class="text-xs text-slate-400">{{ $cat->transactions_count }} {{ Str::plural('transaction', $cat->transactions_count) }}</div>
                            </div>
                            <flux:button size="xs" variant="subtle" square wire:click="edit({{ $cat->id }})">
                                <flux:icon.pencil-square variant="mini" />
                            </flux:button>
                            <flux:button size="xs" variant="subtle" square
                                         wire:click="delete({{ $cat->id }})"
                                         wire:confirm="Delete this category? Transactions will be uncategorised.">
                                <flux:icon.trash variant="mini" class="text-red-500" />
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>
    </div>

</x-layouts.dashboard>
</div>
