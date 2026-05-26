{{-- Shared shell for all authenticated pages: sidebar + header w/ dark-mode toggle --}}
@props(['title' => null, 'subtitle' => null])

<x-layouts.app>
<div x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
    }"
     x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))"
     class="flex h-screen overflow-hidden relative">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen"
         x-cloak
         x-on:click="sidebarOpen = false"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden"></div>

    {{-- Sidebar wrapper:
         - Mobile: fixed, slides in/out via translate
         - Desktop: relative flex-item, always visible --}}
    <div :class="[
             sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
             sidebarCollapsed ? 'lg:w-16' : 'lg:w-64'
         ]"
         class="fixed lg:relative inset-y-0 left-0 z-30 w-64 shrink-0 transition-all duration-300 ease-in-out">
        @include('sidebar')
    </div>

    {{-- Main content --}}
    <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 bg-slate-100 dark:bg-slate-950 transition-colors duration-200 min-w-0">

        {{-- Page header --}}
        <div class="flex items-start justify-between mb-6 gap-3">
            <div class="flex items-center gap-2 min-w-0">

                {{-- Hamburger (mobile only) --}}
                <button x-on:click="sidebarOpen = true"
                        class="lg:hidden -ml-1 p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800 transition-colors shrink-0"
                        aria-label="Open sidebar">
                    <i class="ti ti-menu-2 text-xl leading-none"></i>
                </button>

                <div class="min-w-0">
                    @if($title)
                        <flux:heading size="xl">{{ $title }}</flux:heading>
                    @endif
                    @if($subtitle)
                        <flux:text>{{ $subtitle }}</flux:text>
                    @endif
                </div>
            </div>

            {{-- Right-side actions: theme toggle + page actions --}}
            <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
                <flux:button x-data variant="subtle" square aria-label="Toggle colour scheme"
                             x-on:click="$flux.appearance = $flux.dark ? 'light' : 'dark'">
                    <i x-show="$flux.dark"  class="ti ti-sun  text-base leading-none" x-cloak></i>
                    <i x-show="!$flux.dark" class="ti ti-moon text-base leading-none" x-cloak></i>
                </flux:button>

                {{ $actions ?? '' }}
            </div>
        </div>

        <div class="page-enter">
            {{ $slot }}
        </div>

    </main>

    {{-- First-login welcome wizard (self-hides once onboarded) --}}
    <livewire:onboarding />
</div>
</x-layouts.app>
