{{-- resources/views/livewire/auth/forgot-password.blade.php --}}
<div class="flex min-h-screen">

    {{-- Left panel --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-end">
        <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=1200&auto=format&fit=crop"
             class="absolute inset-0 w-full h-full object-cover" alt="" />
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.85) 0%,rgba(0,0,0,0.2) 60%,transparent 100%)"></div>
        <div class="relative z-10 p-12 text-white">
            <p class="text-3xl font-extrabold leading-tight mb-3">
                Finance Manager.<br>Reset your access securely.
            </p>
            <p class="text-slate-300 text-sm leading-relaxed max-w-sm">
                We'll verify your identity with a one-time code before letting you set a new password.
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

            <flux:heading size="xl" class="mb-1">Forgot password?</flux:heading>
            <flux:text class="mb-8">Follow the steps below to securely reset your password.</flux:text>

            {{-- Step indicator --}}
            <div class="flex items-center justify-center mb-8">
                @foreach([1 => 'Email', 2 => 'OTP', 3 => 'Password'] as $num => $label)
                    <div class="flex flex-col items-center gap-1">
                        <div @class([
                            'w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-colors',
                            'bg-red-600 text-white'   => $step >= $num,
                            'bg-slate-100 dark:bg-slate-800 text-slate-400' => $step < $num,
                        ])>{{ $num }}</div>
                        <span @class([
                            'text-xs font-medium',
                            'text-red-600 dark:text-red-400' => $step >= $num,
                            'text-slate-400'                 => $step < $num,
                        ])>{{ $label }}</span>
                    </div>
                    @if($num < 3)
                        <div @class([
                            'flex-1 h-0.5 mx-2 mb-4 rounded transition-colors',
                            'bg-red-600'                => $step > $num,
                            'bg-slate-200 dark:bg-slate-700' => $step <= $num,
                        ])></div>
                    @endif
                @endforeach
            </div>

            <flux:separator class="mb-8" />

            {{-- Step 1: Email --}}
            @if($step === 1)
                <form wire:submit="sendOtp" class="space-y-5">
                    <flux:field>
                        <flux:label>Email address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="you@example.com" />
                        <flux:error name="email" />
                        <flux:description>We'll send a 6-digit code to your registered email.</flux:description>
                    </flux:field>
                    <flux:button type="submit" variant="primary" class="w-full">Send Verification Code</flux:button>
                </form>
            @endif

            {{-- Step 2: OTP --}}
            @if($step === 2)
                <form wire:submit="verifyOtp" class="space-y-5">
                    <flux:field>
                        <flux:label>6-digit code</flux:label>
                        <flux:input wire:model="otp" type="text" maxlength="6" placeholder="••••••"
                                    class="text-2xl tracking-widest text-center" />
                        <flux:error name="otp" />
                        <flux:description>
                            Code sent to <strong>{{ $email }}</strong>. Expires in 10 minutes.
                        </flux:description>
                    </flux:field>
                    <flux:button type="submit" variant="primary" class="w-full">Verify Code</flux:button>
                    <flux:button wire:click="resendOtp" type="button" variant="ghost" class="w-full">Resend code</flux:button>
                </form>
            @endif

            {{-- Step 3: New password --}}
            @if($step === 3)
                <form wire:submit="resetPassword" class="space-y-5">
                    <flux:field>
                        <flux:label>New password</flux:label>
                        <flux:input wire:model="password" type="password" placeholder="••••••••" viewable />
                        <flux:error name="password" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Confirm password</flux:label>
                        <flux:input wire:model="password_confirmation" type="password" placeholder="••••••••" viewable />
                    </flux:field>
                    <flux:button type="submit" variant="primary" class="w-full">Reset Password</flux:button>
                </form>
            @endif

            <div class="mt-6 text-center">
                <flux:link href="{{ route('login') }}">← Back to login</flux:link>
            </div>

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