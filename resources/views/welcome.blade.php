<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @fluxAppearance
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Finance Manager — Track. Save. Stay in Control.</title>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <style>
        [x-cloak] { display: none !important; }

        /* ── Floating toast ── */
        @keyframes floatUp { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .animate-float { animation: floatUp 3.5s ease-in-out infinite; }

        /* ── Hero animated gradient overlay ── */
        @keyframes gradientFlow {
            0%,100% { background-position: 0% 60%; }
            50%      { background-position: 100% 40%; }
        }
        .hero-gradient {
            background: linear-gradient(
                -45deg,
                rgba(127,29,29,0.88) 0%,
                rgba(2,6,23,0.95)    30%,
                rgba(30,27,75,0.80)  60%,
                rgba(2,6,23,0.92)    100%
            );
            background-size: 400% 400%;
            animation: gradientFlow 18s ease infinite;
        }

        /* ── Scroll-reveal ── */
        [data-reveal] {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.7s cubic-bezier(0.22,1,0.36,1),
                        transform 0.7s cubic-bezier(0.22,1,0.36,1);
        }
        [data-reveal][data-from="left"]  { transform: translateX(-32px); }
        [data-reveal][data-from="right"] { transform: translateX(32px); }
        [data-reveal][data-from="scale"] { transform: scale(0.93) translateY(16px); }

        [data-reveal].visible { opacity:1; transform:none; }
        [data-delay="1"].visible { transition-delay: 0.10s; }
        [data-delay="2"].visible { transition-delay: 0.18s; }
        [data-delay="3"].visible { transition-delay: 0.26s; }
        [data-delay="4"].visible { transition-delay: 0.34s; }
        [data-delay="5"].visible { transition-delay: 0.42s; }
        [data-delay="6"].visible { transition-delay: 0.50s; }

        /* ── Service card shimmer border on hover ── */
        .svc-card {
            position: relative;
            overflow: hidden;
        }
        .svc-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(
                120deg,
                transparent 30%,
                rgba(220,38,38,0.06) 50%,
                transparent 70%
            );
            background-size: 200% 100%;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .svc-card:hover::before {
            opacity: 1;
            animation: shimmer 1.6s linear infinite;
        }
        @keyframes shimmer {
            from { background-position: 200% 0; }
            to   { background-position: -200% 0; }
        }

        /* ── Glow CTA button ── */
        .btn-glow {
            position: relative;
        }
        .btn-glow::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: inherit;
            background: linear-gradient(45deg, #dc2626, #ef4444, #b91c1c);
            background-size: 200% 200%;
            animation: gradientFlow 3s ease infinite;
            z-index: -1;
            filter: blur(10px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .btn-glow:hover::after { opacity: 0.55; }

        /* ── Feature icon hover ── */
        .feat-icon {
            transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1),
                        background-color 0.3s ease;
        }
        .feat-icon:hover {
            transform: scale(1.18) rotate(-8deg);
        }

        /* ── Stat counter pop ── */
        @keyframes statPop {
            0%   { opacity:0; transform: scale(0.7) translateY(8px); }
            65%  { transform: scale(1.06) translateY(-2px); }
            100% { opacity:1; transform: none; }
        }
        .stat-visible .stat-val {
            animation: statPop 0.55s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .stat-visible .stat-val:nth-child(1) { animation-delay: 0.00s; }
        .stat-visible .stat-val:nth-child(2) { animation-delay: 0.08s; }
        .stat-visible .stat-val:nth-child(3) { animation-delay: 0.16s; }
        .stat-visible .stat-val:nth-child(4) { animation-delay: 0.24s; }

        /* ── Nav scroll style ── */
        .nav-scrolled {
            background: rgba(255,255,255,0.97) !important;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.10), 0 1px 4px -1px rgba(0,0,0,0.06) !important;
        }
        .dark .nav-scrolled {
            background: rgba(15,23,42,0.97) !important;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.5) !important;
        }

        /* ── Subtle hero star-field dots ── */
        .hero-dots {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .hero-dots::before, .hero-dots::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(1px 1px at 15%  22%, rgba(255,255,255,0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 38%  68%, rgba(255,255,255,0.35) 0%, transparent 100%),
                radial-gradient(1px 1px at 62%  14%, rgba(255,255,255,0.45) 0%, transparent 100%),
                radial-gradient(1px 1px at 80%  44%, rgba(255,255,255,0.3) 0%, transparent 100%),
                radial-gradient(2px 2px at 28%  85%, rgba(255,255,255,0.25) 0%, transparent 100%),
                radial-gradient(1px 1px at 72%  78%, rgba(255,255,255,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 50%  35%, rgba(255,255,255,0.3) 0%, transparent 100%),
                radial-gradient(2px 2px at 90%  10%, rgba(255,255,255,0.35) 0%, transparent 100%);
            background-size: 100% 100%;
            animation: twinkle 6s ease-in-out infinite alternate;
        }
        .hero-dots::after {
            background-image:
                radial-gradient(1px 1px at 20%  55%, rgba(220,38,38,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 55%  88%, rgba(255,255,255,0.3) 0%, transparent 100%),
                radial-gradient(2px 2px at 85%  25%, rgba(167,139,250,0.3) 0%, transparent 100%),
                radial-gradient(1px 1px at 10%  70%, rgba(255,255,255,0.25) 0%, transparent 100%),
                radial-gradient(1px 1px at 43%  48%, rgba(220,38,38,0.3) 0%, transparent 100%);
            animation-delay: 3s;
        }
        @keyframes twinkle {
            from { opacity: 0.6; }
            to   { opacity: 1; }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-sans antialiased overflow-x-hidden">

{{-- ══════════════════════════════════════
     1. NAVIGATION
══════════════════════════════════════ --}}
<header id="site-nav" x-data="{ open: false }" class="fixed top-3 left-4 right-4 z-50">
    <nav class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-2xl px-5 py-3.5 shadow-sm shadow-slate-200/60 dark:shadow-none">
        <div class="flex items-center justify-between gap-4">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Finance Manager" class="h-8 w-auto object-contain" />
                <div class="leading-tight hidden sm:block">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium tracking-wide">Finance</div>
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white -mt-0.5">Manager</div>
                </div>
            </a>

            {{-- Desktop centre links --}}
            <div class="hidden md:flex items-center gap-0.5">
                <a href="#features"    class="px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Features</a>
                <a href="#how-it-works" class="px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">How It Works</a>
                <a href="#insights"    class="px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Insights</a>
            </div>

            {{-- Desktop right actions --}}
            <div class="hidden md:flex items-center gap-2">
                <button onclick="toggleTheme()" aria-label="Toggle theme"
                        class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="ti ti-sun  text-[17px] leading-none dark:hidden"></i>
                    <i class="ti ti-moon text-[17px] leading-none hidden dark:block"></i>
                </button>
                <a href="{{ route('login') }}"
                   class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                   class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors">
                    Create an account today!
                </a>
            </div>

            {{-- Mobile right: theme + hamburger --}}
            <div class="flex md:hidden items-center gap-1.5">
                <button onclick="toggleTheme()" aria-label="Toggle theme"
                        class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="ti ti-sun  text-[17px] leading-none dark:hidden"></i>
                    <i class="ti ti-moon text-[17px] leading-none hidden dark:block"></i>
                </button>
                <button x-on:click="open = !open" aria-label="Toggle menu"
                        class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i x-show="!open" class="ti ti-menu-2 text-lg leading-none"></i>
                    <i x-show="open"  x-cloak class="ti ti-x text-lg leading-none"></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-cloak
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition duration-150 ease-in"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="md:hidden mt-3 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-0.5">
            <a href="#features"     x-on:click="open=false" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Features</a>
            <a href="#how-it-works" x-on:click="open=false" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">How It Works</a>
            <a href="#insights"     x-on:click="open=false" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Insights</a>
            <div class="pt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('login') }}"
                   class="text-center py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                   class="text-center py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-500 rounded-xl transition-colors">
                    Get Started
                </a>
            </div>
        </div>
    </nav>
</header>


{{-- ══════════════════════════════════════
     2. HERO
══════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center overflow-hidden">
    {{-- Background: image + animated gradient overlay + star dots --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1800&auto=format&fit=crop&q=80"
             class="w-full h-full object-cover object-center" alt="" loading="eager" />
        <div class="hero-gradient absolute inset-0"></div>
        <div class="hero-dots z-[1]"></div>
    </div>

    <div class="hero-parallax relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left: text content --}}
            <div data-reveal>
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-500/10 border border-red-500/20 text-red-300 text-xs font-semibold uppercase tracking-[0.12em] mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                    Smart money management
                </div>

                <h1 class="text-[clamp(2.75rem,6vw,4.75rem)] font-extrabold text-white leading-[1.05] tracking-tight mb-6">
                    Your Finances,<br>Now <span class="text-red-400">Effortless</span>
                </h1>

                <p class="text-lg sm:text-xl text-slate-300 max-w-lg leading-[1.75] mb-10">
                    Stop guessing where your money goes. Track every shilling, visualise spending habits, and build real savings — all in one place.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register') }}"
                       class="btn-glow inline-flex items-center gap-2.5 px-7 py-3.5 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-600/30 text-[15px]">
                        <i class="ti ti-user-plus text-base leading-none"></i>
                        Create an account
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 text-white font-semibold rounded-xl border border-white/20 hover:bg-white/10 transition-all hover:-translate-y-0.5 text-[15px]">
                        Sign In
                    </a>
                </div>

                {{-- Quick stats row --}}
                <div class="mt-12 flex flex-wrap items-center gap-8">
                    <div>
                        <div class="text-2xl font-extrabold text-white tracking-tight">50K+</div>
                        <div class="text-xs text-slate-400 mt-0.5">Active users</div>
                    </div>
                    <div class="w-px h-8 bg-slate-700 hidden sm:block"></div>
                    <div>
                        <div class="text-2xl font-extrabold text-white tracking-tight">KSh 2B+</div>
                        <div class="text-xs text-slate-400 mt-0.5">Transactions tracked</div>
                    </div>
                    <div class="w-px h-8 bg-slate-700 hidden sm:block"></div>
                    <div>
                        <div class="text-2xl font-extrabold text-white tracking-tight">4.9★</div>
                        <div class="text-xs text-slate-400 mt-0.5">User rating</div>
                    </div>
                </div>
            </div>

            {{-- Right: App mockup --}}
            <div class="relative hidden lg:block" data-reveal data-from="right" data-delay="2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_32px_80px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 overflow-hidden">

                    {{-- Fake browser bar --}}
                    <div class="bg-slate-100 dark:bg-slate-800 px-4 py-3 flex items-center gap-2 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                        <div class="flex-1 mx-3 h-5 bg-slate-200 dark:bg-slate-700 rounded-md flex items-center justify-center">
                            <span class="text-[10px] text-slate-400">app.financemanager.co</span>
                        </div>
                    </div>

                    {{-- Dashboard body --}}
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Dashboard</span>
                            <span class="text-xs text-red-600 dark:text-red-400 font-semibold">May 2026</span>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                <div class="text-[10px] text-slate-400 uppercase tracking-wide mb-1.5">Balance</div>
                                <div class="text-base font-bold text-green-600 dark:text-green-400">KSh 284K</div>
                                <div class="text-[10px] text-green-500 mt-0.5">↑ 12.4%</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                <div class="text-[10px] text-slate-400 uppercase tracking-wide mb-1.5">Spent</div>
                                <div class="text-base font-bold text-red-600 dark:text-red-400">KSh 43K</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">38 txns</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                <div class="text-[10px] text-slate-400 uppercase tracking-wide mb-1.5">Goal</div>
                                <div class="text-base font-bold text-blue-600 dark:text-blue-400">68%</div>
                                <div class="text-[10px] text-green-500 mt-0.5">↑ 5%</div>
                            </div>
                        </div>

                        {{-- Bar chart --}}
                        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 rounded-xl px-4 pt-3 h-28 flex items-end gap-1.5 overflow-hidden">
                            @foreach([42,60,38,75,52,88,58,72,45,82,66,96] as $idx => $barH)
                                <div class="flex-1 rounded-t transition-all"
                                     style="height:{{ $barH }}%"
                                     @class([
                                         'bg-red-400/50 dark:bg-red-400/40' => $idx % 3 === 1,
                                         'bg-red-600 dark:bg-red-500'       => $idx % 3 !== 1,
                                     ])></div>
                            @endforeach
                        </div>

                        <div class="flex justify-between px-1">
                            @foreach(['Jan','Mar','Jun','Sep','Dec'] as $m)
                                <span class="text-[10px] text-slate-400">{{ $m }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Floating toast --}}
                <div class="animate-float absolute -bottom-5 -left-6 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-3.5 flex items-center gap-3 whitespace-nowrap z-10">
                    <div class="w-9 h-9 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center shrink-0">
                        <i class="ti ti-check text-green-600 dark:text-green-400 text-base leading-none"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-slate-900 dark:text-white">Budget Goal Reached!</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">Saved KSh 15,000 this month</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     3. STATS
══════════════════════════════════════ --}}
<div class="border-y border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950" data-reveal id="stats-row">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <dl class="grid grid-cols-2 lg:grid-cols-4" id="stats-grid">
            <div class="py-10 px-6 text-center border-r border-b lg:border-b-0 border-slate-200 dark:border-slate-800 group">
                <dd class="stat-val text-4xl lg:text-5xl font-extrabold text-red-600 dark:text-red-400 tracking-tight transition-all duration-300 group-hover:scale-110" data-target="50000" data-suffix="K+">50K+</dd>
                <dt class="text-sm text-slate-500 dark:text-slate-400 mt-2">Active users</dt>
            </div>
            <div class="py-10 px-6 text-center border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-slate-800 group">
                <dd class="stat-val text-4xl lg:text-5xl font-extrabold text-red-600 dark:text-red-400 tracking-tight transition-all duration-300 group-hover:scale-110">KSh 2B+</dd>
                <dt class="text-sm text-slate-500 dark:text-slate-400 mt-2">Transactions tracked</dt>
            </div>
            <div class="py-10 px-6 text-center border-r border-slate-200 dark:border-slate-800 group">
                <dd class="stat-val text-4xl lg:text-5xl font-extrabold text-red-600 dark:text-red-400 tracking-tight transition-all duration-300 group-hover:scale-110">99.9%</dd>
                <dt class="text-sm text-slate-500 dark:text-slate-400 mt-2">Uptime guaranteed</dt>
            </div>
            <div class="py-10 px-6 text-center group">
                <dd class="stat-val text-4xl lg:text-5xl font-extrabold text-red-600 dark:text-red-400 tracking-tight transition-all duration-300 group-hover:scale-110">4.9★</dd>
                <dt class="text-sm text-slate-500 dark:text-slate-400 mt-2">Average rating</dt>
            </div>
        </dl>
    </div>
</div>


{{-- ══════════════════════════════════════
     4. WHAT WE OFFER — Services Grid
══════════════════════════════════════ --}}
<section id="features" class="py-24 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16" data-reveal>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-red-600 dark:text-red-400 mb-4">What We Offer</p>
            <h2 class="text-[clamp(2rem,4vw,3.5rem)] font-extrabold text-slate-400 dark:text-slate-500 leading-[1.1] tracking-tight mb-4">
                Seamless Finance Management
            </h2>
            <p class="text-base text-slate-600 dark:text-slate-400 max-w-lg mx-auto leading-relaxed">
                Your gateway to effortless money control — everything you need to budget smarter, save more, and stress less.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['featured' => true,  'icon' => 'ti-chart-pie',        'title' => 'Budget Tracking',   'desc' => 'Set category limits and see exactly where every shilling goes with real-time automatic tracking.'],
                ['featured' => false, 'icon' => 'ti-file-description',  'title' => 'Expense Reports',   'desc' => 'Detailed breakdowns of every transaction, sortable by date, category, or amount in seconds.'],
                ['featured' => false, 'icon' => 'ti-target',            'title' => 'Savings Goals',     'desc' => 'Set targets, visualise progress with charts, and receive suggestions to hit them ahead of schedule.'],
                ['featured' => false, 'icon' => 'ti-trending-up',       'title' => 'Investment Watch',  'desc' => 'Monitor portfolio performance and track returns alongside your everyday spending and budgets.'],
                ['featured' => false, 'icon' => 'ti-bell',              'title' => 'Bill Reminders',    'desc' => 'Automated smart alerts when payments are due so you never miss a deadline or incur a late fee.'],
                ['featured' => false, 'icon' => 'ti-download',          'title' => 'Data Exports',      'desc' => 'Download monthly or custom-range reports as PDF or CSV with a single click, ready to share.'],
            ] as $card)
            <div data-reveal data-delay="{{ $loop->index + 1 }}"
                 @class([
                'svc-card group relative rounded-3xl p-7 transition-all duration-300 hover:-translate-y-1',
                'bg-red-600'                                                                                             => $card['featured'],
                'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-red-200 dark:hover:border-red-900/50 hover:shadow-xl hover:shadow-slate-200/60 dark:hover:shadow-slate-900' => !$card['featured'],
            ])>
                {{-- Icon --}}
                <div @class([
                    'w-11 h-11 rounded-2xl flex items-center justify-center mb-5',
                    'bg-white/20'                                           => $card['featured'],
                    'bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30' => !$card['featured'],
                ])>
                    <i @class([
                        'ti text-xl leading-none',
                        $card['icon'],
                        'text-white'                       => $card['featured'],
                        'text-red-600 dark:text-red-400'   => !$card['featured'],
                    ])></i>
                </div>

                {{-- Title --}}
                <h3 @class([
                    'text-base font-bold mb-2',
                    'text-white'                         => $card['featured'],
                    'text-slate-900 dark:text-white'     => !$card['featured'],
                ])>{{ $card['title'] }}</h3>

                {{-- Description --}}
                <p @class([
                    'text-sm leading-relaxed',
                    'text-red-100'                         => $card['featured'],
                    'text-slate-500 dark:text-slate-400'   => !$card['featured'],
                ])>{{ $card['desc'] }}</p>

                {{-- Arrow --}}
                <div @class([
                    'absolute bottom-6 right-6 w-8 h-8 rounded-xl flex items-center justify-center',
                    'bg-white/20'                                         => $card['featured'],
                    'bg-red-50 dark:bg-red-900/20 group-hover:bg-red-100 dark:group-hover:bg-red-900/40' => !$card['featured'],
                ])>
                    <i @class([
                        'ti ti-arrow-right text-sm leading-none',
                        'text-white'                       => $card['featured'],
                        'text-red-600 dark:text-red-400'   => !$card['featured'],
                    ])></i>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     5. FEATURE HIGHLIGHTS STRIP
══════════════════════════════════════ --}}
<section id="how-it-works" class="py-20 bg-white dark:bg-slate-950 border-y border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-3 gap-10 text-center">
            @foreach([
                ['icon' => 'ti-devices',     'delay' => 1, 'title' => 'Always Accessible',   'desc' => 'Works seamlessly on desktop, tablet, and mobile. Manage your money wherever life takes you.'],
                ['icon' => 'ti-refresh',     'delay' => 2, 'title' => 'Instant Updates',     'desc' => 'Transactions sync in real time across all your sessions. No delays, no stale data — always current.'],
                ['icon' => 'ti-shield-check','delay' => 3, 'title' => 'Bank-Level Security', 'desc' => 'End-to-end encryption with OTP verification on every sensitive action to keep your data safe.'],
            ] as $feat)
            <div class="flex flex-col items-center" data-reveal data-delay="{{ $feat['delay'] }}">
                <div class="feat-icon w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 flex items-center justify-center mb-5 cursor-pointer">
                    <i class="ti {{ $feat['icon'] }} text-xl leading-none text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">{{ $feat['title'] }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs">{{ $feat['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     6. NEWS / BLOG PREVIEW
══════════════════════════════════════ --}}
<section id="insights" class="py-24 bg-slate-50 dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14" data-reveal>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-red-600 dark:text-red-400 mb-4">Stay Informed</p>
            <h2 class="text-[clamp(1.8rem,3.5vw,2.8rem)] font-extrabold text-slate-900 dark:text-white leading-tight tracking-tight">
                Finance Insights
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['tag' => 'Savings',   'color' => 'text-red-600 dark:text-red-400',     'img' => 'https://images.unsplash.com/photo-1579621970795-87facc2f976d?w=800&auto=format&fit=crop',  'title' => 'How to Build a 3-Month Emergency Fund',    'desc' => 'A practical, step-by-step plan to save 3 months of expenses without disrupting your lifestyle or sacrificing joy.'],
                ['tag' => 'Budgeting', 'color' => 'text-blue-600 dark:text-blue-400',   'img' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&auto=format&fit=crop',  'title' => 'The 50/30/20 Budget Rule Explained',       'desc' => 'Learn how splitting your income into needs, wants, and savings can permanently transform your financial health.'],
                ['tag' => 'Tips',      'color' => 'text-emerald-600 dark:text-emerald-400', 'img' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop','title' => 'Smart Ways to Cut Monthly Expenses',       'desc' => 'Practical cuts that won\'t hurt your quality of life, plus tools to track and cancel hidden subscriptions.'],
            ] as $post)
            <article data-reveal data-delay="{{ $loop->index + 1 }}"
                     class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 hover:-translate-y-1 transition-all duration-300 group">
                <div class="h-44 overflow-hidden">
                    <img src="{{ $post['img'] }}" alt="{{ $post['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                </div>
                <div class="p-6">
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] {{ $post['color'] }} mb-3">{{ $post['tag'] }}</div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2 leading-snug">{{ $post['title'] }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-5">{{ $post['desc'] }}</p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $post['color'] }} hover:underline underline-offset-2">
                        Learn more <i class="ti ti-arrow-right text-xs leading-none"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     7. FINAL CTA
══════════════════════════════════════ --}}
<section class="py-24 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden">
            <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1400&auto=format&fit=crop&q=80"
                 class="absolute inset-0 w-full h-full object-cover object-center" alt="" loading="lazy" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/97 via-slate-900/80 to-slate-900/40"></div>

            <div class="relative z-10 px-8 sm:px-14 lg:px-20 py-20" data-reveal>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-red-400 mb-5">Digital Finance Services</p>
                <h2 class="text-[clamp(2rem,4vw,3.5rem)] font-extrabold text-white leading-[1.08] tracking-tight mb-6 max-w-2xl">
                    Simplify Payments,<br>Amplify Your Wealth.
                </h2>
                <p class="text-slate-300 leading-[1.75] max-w-md mb-10">
                    Join thousands already using Finance Manager to cut waste and build better money habits. Free to start — no credit card needed.
                </p>
                <a href="{{ route('register') }}"
                   class="btn-glow inline-flex items-center gap-2.5 px-8 py-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-600/30 text-base">
                    <i class="ti ti-user-plus text-base leading-none"></i>
                    Create your account today
                </a>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     8. FOOTER
══════════════════════════════════════ --}}
<footer class="bg-slate-900 dark:bg-slate-950 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-10 mb-12">

            {{-- Brand column --}}
            <div class="col-span-2">
                <a href="/" class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Finance Manager" class="h-8 w-auto object-contain brightness-0 invert opacity-80" />
                    <div class="leading-tight">
                        <div class="text-[11px] text-slate-500 font-medium">Finance</div>
                        <div class="text-sm font-extrabold text-white -mt-0.5">Manager</div>
                    </div>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed max-w-xs">
                    Built to help you track every shilling, reach every goal, and spend smarter every single day.
                </p>
                <div class="flex items-center gap-2.5 mt-5">
                    @foreach(['ti-brand-twitter','ti-brand-linkedin','ti-brand-instagram'] as $icon)
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-red-600 flex items-center justify-center transition-colors group">
                        <i class="ti {{ $icon }} text-sm leading-none text-slate-400 group-hover:text-white"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Product links --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500 mb-4">Product</p>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="#features"     class="hover:text-white transition-colors">Features</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a></li>
                    <li><a href="{{ route('login') }}"    class="hover:text-white transition-colors">Dashboard</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Get Started Free</a></li>
                </ul>
            </div>

            {{-- Resources links --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500 mb-4">Resources</p>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="#insights" class="hover:text-white transition-colors">Finance Insights</a></li>
                    <li><a href="#"         class="hover:text-white transition-colors">Help Centre</a></li>
                    <li><a href="#"         class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="#"         class="hover:text-white transition-colors">Terms of Service</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500 mb-4">Contact</p>
                <ul class="space-y-3 text-sm text-slate-400">
                    <li class="flex items-start gap-2">
                        <i class="ti ti-mail text-sm leading-none mt-0.5 shrink-0"></i>
                        <span>support@financemanager.co</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ti ti-map-pin text-sm leading-none mt-0.5 shrink-0"></i>
                        <span>Nairobi, Kenya</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">© {{ date('Y') }} Finance Manager — Built to help you save more.</p>
            <div class="flex items-center gap-5 text-xs text-slate-500">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
                <a href="#" class="hover:text-white transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<script>
/* ── Theme toggle ── */
function toggleTheme() {
    const isDark = document.documentElement.classList.contains('dark');
    window.Flux.applyAppearance(isDark ? 'light' : 'dark');
}

document.addEventListener('DOMContentLoaded', () => {

    /* ── Navbar: solidify on scroll ── */
    const nav = document.querySelector('#site-nav nav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            nav.classList.add('nav-scrolled');
        } else {
            nav.classList.remove('nav-scrolled');
        }
    }, { passive: true });

    /* ── Scroll-reveal ── */
    const revealObs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('[data-reveal]').forEach(el => revealObs.observe(el));

    /* ── Stats pop animation when row becomes visible ── */
    const statsRow = document.getElementById('stats-grid');
    if (statsRow) {
        const statsObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('stat-visible');
                    statsObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });
        statsObs.observe(statsRow);
    }

    /* ── Service card hover: glow shadow tint ── */
    document.querySelectorAll('.svc-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            const featured = card.classList.contains('bg-red-600');
            card.style.boxShadow = featured
                ? '0 24px 60px -8px rgba(220,38,38,0.45), 0 8px 20px -4px rgba(220,38,38,0.25)'
                : '0 20px 50px -8px rgba(0,0,0,0.14), 0 6px 16px -4px rgba(220,38,38,0.08)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.boxShadow = '';
        });
    });

    /* ── Parallax: hero content drifts slightly on scroll ── */
    const heroContent = document.querySelector('.hero-parallax');
    if (heroContent) {
        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            heroContent.style.transform = `translateY(${y * 0.18}px)`;
        }, { passive: true });
    }

});
</script>
</body>
</html>
