<div class="flex min-h-screen">

    {{-- Left Panel (always dark photo) --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-end">
        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&auto=format&fit=crop"
             class="absolute inset-0 w-full h-full object-cover" alt="" />
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.85) 0%,rgba(0,0,0,0.2) 60%,transparent 100%)"></div>
        <div class="relative z-10 p-12 text-white">
            <p class="text-3xl font-extrabold leading-tight mb-3">
                Finance Manager.<br>Start your journey today.
            </p>
            <p class="text-slate-300 text-sm leading-relaxed max-w-sm">
                Join thousands managing their money smarter — budgets, goals, and insights all in one place.
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

            @if($step === 1)
                <flux:heading size="xl" class="mb-1">Create account</flux:heading>
                <flux:text class="mb-8">
                    Already have an account?
                    <flux:link href="{{ route('login') }}">Sign in</flux:link>
                </flux:text>

                <form wire:submit="register" class="space-y-5">
                    <flux:field>
                        <flux:label>Full name</flux:label>
                        <flux:input wire:model="name" type="text" placeholder="John Doe" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="you@example.com" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Password</flux:label>
                        <flux:input wire:model="password" type="password" placeholder="••••••••" viewable />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Confirm password</flux:label>
                        <flux:input wire:model="password_confirmation" type="password" placeholder="••••••••" viewable />
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full">Create Account</flux:button>
                </form>
            @endif

            @if($step === 2)
                <flux:heading size="xl" class="mb-1">Verify your email</flux:heading>
                <flux:text class="mb-8">
                    We sent a 6-digit code to
                    <strong class="text-slate-900 dark:text-white">{{ $email }}</strong>
                </flux:text>

                @if(session('status'))
                    <flux:callout variant="success" icon="check-circle" class="mb-6">
                        {{ session('status') }}
                    </flux:callout>
                @endif

                <form wire:submit="verifyOtp" class="space-y-5">
                    <flux:field>
                        <flux:label>6-digit code</flux:label>
                        <flux:input wire:model="otp" type="text" maxlength="6" placeholder="••••••"
                                    class="text-2xl tracking-widest text-center" />
                        <flux:error name="otp" />
                        <flux:description>Expires in 10 minutes.</flux:description>
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full">Verify & Continue</flux:button>
                    <flux:button wire:click="resendOtp" type="button" variant="ghost" class="w-full">Resend code</flux:button>
                </form>
            @endif

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
