<div>
@if($show)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>

        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md overflow-hidden">

            {{-- Step indicator --}}
            <div class="flex gap-1.5 px-6 pt-6">
                @for($i = 1; $i <= 3; $i++)
                    <div class="h-1.5 flex-1 rounded-full {{ $i <= $step ? 'bg-red-500' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
                @endfor
            </div>

            <div class="px-6 py-6">
                {{-- ── STEP 1: WELCOME ── --}}
                @if($step === 1)
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <i class="ti ti-wallet text-3xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <flux:heading size="lg">Welcome to Finance Manager 👋</flux:heading>
                        <flux:text class="mt-2">
                            Plan your month, track every shilling, and watch your savings carry forward.
                            Let's get you set up in a few seconds.
                        </flux:text>
                    </div>
                    <div class="mt-6">
                        <flux:button variant="primary" class="w-full" wire:click="next">Next</flux:button>
                    </div>

                {{-- ── STEP 2: SET PAY CYCLE ── --}}
                @elseif($step === 2)
                    <div class="text-center mb-5">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="ti ti-calendar-dollar text-3xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <flux:heading size="lg">When do you get paid?</flux:heading>
                        <flux:text class="mt-2">
                            Pick the day your salary usually lands. From that day, the app plans for the
                            upcoming month — so this month's pay funds next month's budget.
                        </flux:text>
                    </div>

                    <form wire:submit="savePayCycle">
                        <flux:field>
                            <flux:label>Pay cycle start day</flux:label>
                            <flux:input wire:model="pay_cycle_day" type="number" min="1" max="31" />
                            <flux:error name="pay_cycle_day" />
                            <flux:text size="sm" class="text-slate-400 mt-1">
                                Day 1–31. If a month is shorter (e.g. February), it falls to that month's last day.
                            </flux:text>
                        </flux:field>

                        <div class="flex gap-2 mt-6">
                            <flux:button type="button" variant="ghost" wire:click="back">Back</flux:button>
                            <flux:button type="submit" variant="primary" class="flex-1">Save &amp; continue</flux:button>
                        </div>
                    </form>

                {{-- ── STEP 3: SUCCESS ── --}}
                @else
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="ti ti-circle-check text-3xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <flux:heading size="lg">Pay cycle set successfully</flux:heading>
                        <flux:text class="mt-2">
                            You're all set — your budget months now run on day {{ $pay_cycle_day }}.
                            Want to change it later? You'll find it any time under
                            <strong>Settings → Pay cycle</strong>.
                        </flux:text>
                    </div>
                    <div class="mt-6">
                        <flux:button variant="primary" class="w-full" wire:click="finish">Done</flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
</div>
