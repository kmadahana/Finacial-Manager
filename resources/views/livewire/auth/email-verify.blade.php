<div class="flex min-h-screen">

    {{-- Left panel (always dark photo) --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-end">
        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&auto=format&fit=crop"
             class="absolute inset-0 w-full h-full object-cover" alt="" />
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.85) 0%,rgba(0,0,0,0.2) 60%,transparent 100%)"></div>
        <div class="relative z-10 p-12 text-white">
            <p class="text-3xl font-extrabold leading-tight mb-3">
                Finance Manager.<br>Almost there!
            </p>
            <p class="text-slate-300 text-sm leading-relaxed max-w-sm">
                One last step — verify your email to activate your account and start managing your finances.
            </p>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white dark:bg-slate-950 px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-slate-900 dark:text-white">Finance Manager</span>
            </div>

            @if(!$verified)
                <flux:link href="{{ route('login') }}" class="inline-flex items-center gap-1 mb-8 text-sm">
                    ← Back to Sign In
                </flux:link>

                <flux:heading size="xl" class="mb-1">Verify your email</flux:heading>
                <flux:text class="mb-8">
                    We sent a 6-digit code to <strong class="text-slate-900 dark:text-white">{{ $email }}</strong>
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

            @else
                {{-- Success state --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-50 dark:bg-green-950 rounded-full flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <flux:heading size="xl" class="mb-3">You're all set!</flux:heading>
                    <flux:text class="mb-10">
                        Your email has been verified.<br>Welcome to Finance Manager!
                    </flux:text>
                    <flux:button href="{{ route('login') }}" variant="primary" class="w-full">
                        Go to Sign In
                    </flux:button>
                </div>
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
