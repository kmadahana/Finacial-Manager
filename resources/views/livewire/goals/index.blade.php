<div>
<x-layouts.dashboard title="Goals" subtitle="Set savings targets and watch them grow">
    <x-slot name="actions">
        <flux:button variant="primary" icon="plus" wire:click="openCreate">New goal</flux:button>
    </x-slot>

    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-4">
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- Summary --}}
    @if($goals->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <flux:card>
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Active goals</flux:text>
                <div class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $goals->count() }}</div>
            </flux:card>
            <flux:card>
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Total saved</flux:text>
                <div class="text-2xl font-semibold text-green-500">KSh {{ number_format($totalSaved, 2) }}</div>
            </flux:card>
            <flux:card>
                <flux:text size="sm" class="text-slate-500 dark:text-slate-400 mb-2">Total target</flux:text>
                <div class="text-2xl font-semibold text-slate-900 dark:text-white">KSh {{ number_format($totalTarget, 2) }}</div>
            </flux:card>
        </div>
    @endif

    {{-- Form --}}
    @if($showForm)
        <flux:card class="mb-6 border-red-200 dark:border-red-600/30">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="sm">{{ $editing_id ? 'Edit goal' : 'New goal' }}</flux:heading>
                <flux:button size="xs" variant="subtle" square wire:click="$set('showForm', false)" aria-label="Close">
                    <flux:icon.x-mark variant="mini" />
                </flux:button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Goal name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g. New laptop, Emergency fund" />
                    <flux:error name="name" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>Target (KSh)</flux:label>
                        <flux:input wire:model="target_amount" type="number" step="0.01" min="1" placeholder="0.00" />
                        <flux:error name="target_amount" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Saved so far (KSh)</flux:label>
                        <flux:input wire:model="current_amount" type="number" step="0.01" min="0" placeholder="0.00" />
                        <flux:error name="current_amount" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Target date (optional)</flux:label>
                        <flux:input wire:model="target_date" type="date" />
                        <flux:error name="target_date" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Colour</flux:label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colorOptions as $c)
                            <button type="button" wire:click="$set('color', '{{ $c }}')"
                                    class="w-8 h-8 rounded-lg ring-2 ring-offset-2 ring-offset-white dark:ring-offset-slate-900 transition
                                           {{ $color === $c ? 'ring-slate-900 dark:ring-white' : 'ring-transparent' }}"
                                    style="background:{{ $c }}"></button>
                        @endforeach
                    </div>
                </flux:field>

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

                <div class="flex gap-2 pt-2">
                    <flux:button type="submit" variant="primary">
                        {{ $editing_id ? 'Update goal' : 'Add goal' }}
                    </flux:button>
                    <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)">Cancel</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Goals grid --}}
    @if($goals->isEmpty())
        <flux:card>
            <div class="py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <i class="ti ti-target text-2xl text-slate-400"></i>
                </div>
                <flux:heading size="sm" class="mb-1">No goals yet</flux:heading>
                <flux:text size="sm" class="mb-4">Start by setting a savings goal — a holiday, an emergency fund, anything.</flux:text>
                <flux:button variant="primary" icon="plus" wire:click="openCreate">Create your first goal</flux:button>
            </div>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($goals as $g)
                @php
                    $pct       = $g->progress_percent;
                    $remaining = max(0, $g->target_amount - $g->current_amount);
                    $isDone    = $pct >= 100;
                @endphp
                <flux:card wire:key="goal-{{ $g->id }}">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                             style="background:{{ $g->color }}20">
                            <i class="ti {{ $g->icon }} text-2xl" style="color:{{ $g->color }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <flux:heading size="sm" class="truncate">{{ $g->name }}</flux:heading>
                            <flux:text size="sm">
                                @if($g->target_date)
                                    Target: {{ $g->target_date->format('M j, Y') }}
                                    ({{ $g->target_date->diffForHumans(['parts' => 1]) }})
                                @else
                                    No deadline
                                @endif
                            </flux:text>
                        </div>
                        <div class="flex items-center gap-1">
                            <flux:button size="xs" variant="subtle" square wire:click="edit({{ $g->id }})">
                                <flux:icon.pencil-square variant="mini" />
                            </flux:button>
                            <flux:button size="xs" variant="subtle" square
                                         wire:click="delete({{ $g->id }})"
                                         wire:confirm="Delete this goal?">
                                <flux:icon.trash variant="mini" class="text-red-500" />
                            </flux:button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="flex justify-between mb-1.5">
                            <flux:text size="sm" class="font-medium">
                                KSh {{ number_format($g->current_amount, 0) }}
                                <span class="text-slate-400">/ KSh {{ number_format($g->target_amount, 0) }}</span>
                            </flux:text>
                            <flux:text size="sm" class="font-medium {{ $isDone ? 'text-green-500' : '' }}">
                                {{ $pct }}%
                            </flux:text>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-700"
                                 style="width:{{ $pct }}%; background:{{ $g->color }}"></div>
                        </div>
                        @if(!$isDone)
                            <flux:text size="sm" class="text-slate-400 mt-1.5">
                                KSh {{ number_format($remaining, 0) }} to go
                            </flux:text>
                        @else
                            <flux:text size="sm" class="text-green-500 mt-1.5 font-medium">
                                🎉 Goal reached!
                            </flux:text>
                        @endif
                    </div>

                    @if(!$isDone)
                        <div class="flex gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <flux:button size="xs" variant="subtle" wire:click="contribute({{ $g->id }}, 500)">+ KSh 500</flux:button>
                            <flux:button size="xs" variant="subtle" wire:click="contribute({{ $g->id }}, 1000)">+ KSh 1,000</flux:button>
                            <flux:button size="xs" variant="subtle" wire:click="contribute({{ $g->id }}, 5000)">+ KSh 5,000</flux:button>
                        </div>
                    @endif
                </flux:card>
            @endforeach
        </div>
    @endif

</x-layouts.dashboard>
</div>
