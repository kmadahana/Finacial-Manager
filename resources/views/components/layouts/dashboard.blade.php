{{-- Shared shell for all authenticated pages: sidebar + header w/ dark-mode toggle --}}
@props(['title' => null, 'subtitle' => null])

<x-layouts.app>
{{-- h-screen + overflow-hidden pins the shell to the viewport.
     Sidebar stays put; only <main> scrolls. --}}
<div class="flex h-screen overflow-hidden">

    {{-- Sidebar (fixed within viewport) --}}
    @include('sidebar')

    {{-- Main content (scrolls independently) --}}
    <main class="flex-1 overflow-y-auto overflow-x-hidden p-6 bg-slate-100 dark:bg-slate-950 transition-colors duration-200">

        {{-- Page header --}}
        <div class="flex items-start justify-between mb-6 gap-4">
            <div class="min-w-0">
                @if($title)
                    <flux:heading size="xl">{{ $title }}</flux:heading>
                @endif
                @if($subtitle)
                    <flux:text>{{ $subtitle }}</flux:text>
                @endif
            </div>

            {{-- Right-side actions: theme toggle FIRST (left), then page actions (right).
                 Both sit on the same row, separated by a gap. No dropdowns. --}}
            <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">

                {{-- Single-button dark/light toggle.
                     - On first load, $flux.appearance defaults to 'system' and follows the
                       OS preference. The icon below shows the *resolved* state via $flux.dark.
                     - Click flips between an explicit 'light' / 'dark' (persisted to
                       localStorage by Flux), so subsequent visits respect the choice. --}}
                <flux:button x-data variant="subtle" square aria-label="Toggle colour scheme"
                             x-on:click="$flux.appearance = $flux.dark ? 'light' : 'dark'">
                    <i x-show="$flux.dark"  class="ti ti-sun  text-base leading-none" x-cloak></i>
                    <i x-show="!$flux.dark" class="ti ti-moon text-base leading-none" x-cloak></i>
                </flux:button>

                {{-- Slot for extra page actions (New transaction, etc.) --}}
                {{ $actions ?? '' }}
            </div>
        </div>

        {{ $slot }}

    </main>
</div>
</x-layouts.app>
