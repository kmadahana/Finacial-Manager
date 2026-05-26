<div class="flex min-h-screen">

    {{-- Left Panel (always dark photo) --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-end">
        <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=1200&auto=format&fit=crop"
             class="absolute inset-0 w-full h-full object-cover" alt="" />
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.85) 0%,rgba(0,0,0,0.2) 60%,transparent 100%)"></div>
        <div class="relative z-10 p-12 text-white">
            <p class="text-3xl font-extrabold leading-tight mb-3">
                Finance Manager.<br>Track smarter. Save more.
            </p>
            <p class="text-slate-300 text-sm leading-relaxed max-w-sm">
                Get a clear picture of your finances — every shilling tracked, every goal within reach.
            </p>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white dark:bg-slate-950 px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="flex items-center gap-2.5 mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Finance Manager" class="h-9 w-auto object-contain shrink-0" />
                <div class="leading-tight">
                    <div class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Finance</div>
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white -mt-0.5">Manager</div>
                </div>
            </div>

            <flux:heading size="xl" class="mb-1">Sign in</flux:heading>
            <flux:text class="mb-8">
                Don't have an account?
                <flux:link href="{{ route('register') }}">Sign up</flux:link>
            </flux:text>

            @if (session('status'))
                <flux:callout variant="success" icon="check-circle" class="mb-6">
                    {{ session('status') }}
                </flux:callout>
            @endif

            <form wire:submit="login" class="space-y-5">
                <flux:field>
                    <flux:label>Email address</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="you@example.com" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <div class="flex justify-between items-center mb-1">
                        <flux:label>Password</flux:label>
                        <flux:link href="{{ route('password.request') }}" class="text-xs">Forgot password?</flux:link>
                    </div>
                    <flux:input wire:model="password" type="password" placeholder="••••••••" viewable />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="remember" id="remember" label="Remember me" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full">Sign In</flux:button>
            </form>

            {{-- Dark mode toggle --}}
            <div class="mt-8 flex justify-center">
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun" />
                    <flux:radio value="dark"  icon="moon" />
                    <flux:radio value="system" icon="computer-desktop" />
                </flux:radio.group>
            </div>

        </div>
    </div>
</div>
