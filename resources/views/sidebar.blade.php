@php use Illuminate\Support\Facades\Storage; @endphp
<aside class="h-full flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 overflow-hidden">

    {{-- Logo --}}
    <div :class="sidebarCollapsed ? 'justify-center px-0' : 'px-5 gap-3'"
         class="flex items-center py-5 border-b border-slate-100 dark:border-slate-800 shrink-0 transition-all duration-300">
        <img src="{{ asset('images/logo.png') }}" alt="Finance Manager" class="h-8 w-auto object-contain shrink-0" />
        <div x-show="!sidebarCollapsed" x-cloak x-transition:enter="transition-opacity duration-200 delay-100"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-100" x-transition:leave-end="opacity-0"
             class="leading-tight min-w-0">
            <div class="text-[11px] text-slate-400 dark:text-slate-500 font-medium truncate">Finance</div>
            <div class="text-sm font-extrabold text-slate-900 dark:text-white -mt-0.5 truncate">Manager</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 space-y-0.5" :class="sidebarCollapsed ? 'px-1.5' : 'px-3'">
        @php
            $navItems = [
                ['route' => 'dashboard',    'icon' => 'ti-layout-dashboard', 'label' => 'Dashboard'],
                ['route' => 'income',       'icon' => 'ti-cash',             'label' => 'Income'],
                ['route' => 'transactions', 'icon' => 'ti-arrows-exchange',  'label' => 'Transactions'],
                ['route' => 'goals',        'icon' => 'ti-target',           'label' => 'Goals'],
                ['route' => 'categories',   'icon' => 'ti-tag',              'label' => 'Categories'],
                ['route' => 'reports',      'icon' => 'ti-chart-bar',        'label' => 'Reports'],
                ['route' => 'settings',     'icon' => 'ti-settings',         'label' => 'Settings'],
            ];
        @endphp

        @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               title="{{ $item['label'] }}"
               :class="sidebarCollapsed ? 'justify-center px-2' : 'px-3 gap-3'"
               @class([
                   'flex items-center py-2.5 rounded-xl text-sm font-medium transition-colors',
                   'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400'                                                             => $isActive,
                   'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' => !$isActive,
               ])>
                <i class="ti {{ $item['icon'] }} text-base leading-none shrink-0"></i>
                <span x-show="!sidebarCollapsed" x-cloak
                      x-transition:enter="transition-opacity duration-150 delay-100"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-100" x-transition:leave-end="opacity-0">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- Collapse toggle (desktop only) --}}
    <div class="hidden lg:flex justify-center px-2 pb-1 shrink-0">
        <button type="button"
                x-on:click="sidebarCollapsed = !sidebarCollapsed"
                :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                :class="sidebarCollapsed ? 'justify-center' : ''">
            <i :class="sidebarCollapsed ? 'ti-layout-sidebar-right-expand' : 'ti-layout-sidebar-left-collapse'"
               class="ti text-base leading-none shrink-0"></i>
            <span x-show="!sidebarCollapsed" x-cloak class="whitespace-nowrap">Collapse</span>
        </button>
    </div>

    {{-- User & Logout --}}
    <div class="border-t border-slate-100 dark:border-slate-800 py-4 space-y-2 shrink-0 transition-all duration-300"
         :class="sidebarCollapsed ? 'px-1.5' : 'px-4'">

        {{-- User info --}}
        <div :class="sidebarCollapsed ? 'justify-center' : 'gap-3'" class="flex items-center">
            @if(auth()->user()->avatar)
                <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-9 h-9 rounded-full object-cover shrink-0" />
            @else
                <div class="w-9 h-9 bg-red-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
            <div x-show="!sidebarCollapsed" x-cloak
                 x-transition:enter="transition-opacity duration-150 delay-100"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-100" x-transition:leave-end="opacity-0"
                 class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</div>
            </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    :class="sidebarCollapsed ? 'justify-center px-0' : 'gap-2 px-3'"
                    :title="sidebarCollapsed ? 'Logout' : ''"
                    class="w-full flex items-center py-2 text-sm text-slate-500 dark:text-slate-400
                           hover:bg-red-50 dark:hover:bg-red-600/10 hover:text-red-500 dark:hover:text-red-400
                           rounded-xl transition-colors">
                <i class="ti ti-logout text-base leading-none"></i>
                <span x-show="!sidebarCollapsed" x-cloak
                      x-transition:enter="transition-opacity duration-150 delay-100"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-100" x-transition:leave-end="opacity-0">
                    Logout
                </span>
            </button>
        </form>
    </div>

</aside>
