<aside class="w-64 shrink-0 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 min-h-screen">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-100 dark:border-slate-800">
        <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center shrink-0">
            <i class="ti ti-currency-dollar text-white text-lg leading-none"></i>
        </div>
        <span class="font-bold text-slate-900 dark:text-white text-sm">Finance Manager</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5">
        @php
            $navItems = [
                ['route' => 'dashboard',    'icon' => 'ti-layout-dashboard', 'label' => 'Dashboard'],
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
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ $isActive
                          ? 'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400'
                          : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="ti {{ $item['icon'] }} text-base leading-none"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- User & Logout --}}
    <div class="border-t border-slate-100 dark:border-slate-800 px-4 py-4 space-y-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-red-600 rounded-full flex items-center justify-center text-white text-sm font-semibold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-500 dark:text-slate-400
                           hover:bg-red-50 dark:hover:bg-red-600/10 hover:text-red-500 dark:hover:text-red-400
                           rounded-xl transition-colors">
                <i class="ti ti-logout text-base leading-none"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>
