<div>
@php use Illuminate\Support\Facades\Storage; @endphp
<x-layouts.dashboard title="Settings" subtitle="Manage your account">

    <div class="max-w-3xl space-y-6">

        {{-- ── AVATAR ────────────────────────────────────────── --}}
        <flux:card>
            <div class="mb-5">
                <flux:heading size="sm">Profile picture</flux:heading>
                <flux:text size="sm">Upload a photo to use as your avatar</flux:text>
            </div>

            @if(session('avatar-success'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    {{ session('avatar-success') }}
                </flux:callout>
            @endif

            <div class="flex items-center gap-6">
                {{-- Current avatar --}}
                <div class="shrink-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}"
                             alt="Avatar"
                             class="w-20 h-20 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-red-600 flex items-center justify-center text-white text-2xl font-semibold ring-2 ring-slate-200 dark:ring-slate-700">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Upload controls --}}
                <div class="flex-1 space-y-3">
                    <div x-data="{ preview: null }">
                        <label class="block">
                            <span class="sr-only">Choose file</span>
                            <input type="file" wire:model="avatar"
                                   accept="image/*"
                                   x-on:change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                   class="block w-full text-sm text-slate-500 dark:text-slate-400
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-sm file:font-medium
                                          file:bg-red-50 file:text-red-600
                                          dark:file:bg-red-600/10 dark:file:text-red-400
                                          hover:file:bg-red-100 dark:hover:file:bg-red-600/20
                                          cursor-pointer" />
                        </label>

                        <template x-if="preview">
                            <img :src="preview" class="mt-3 w-16 h-16 rounded-full object-cover ring-2 ring-red-400" />
                        </template>

                        @error('avatar')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <flux:button wire:click="uploadAvatar" variant="primary" size="sm">
                            Upload photo
                        </flux:button>
                        @if(auth()->user()->avatar)
                            <flux:button wire:click="removeAvatar" variant="ghost" size="sm"
                                         wire:confirm="Remove your profile picture?">
                                Remove
                            </flux:button>
                        @endif
                    </div>
                    <flux:text size="sm" class="text-slate-400">JPG, PNG, WebP or GIF · Max 2 MB</flux:text>
                </div>
            </div>
        </flux:card>

        {{-- ── PROFILE ───────────────────────────────────────── --}}
        <flux:card>
            <div class="mb-5">
                <flux:heading size="sm">Profile</flux:heading>
                <flux:text size="sm">Update your name and email address</flux:text>
            </div>

            @if(session('profile-success'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    {{ session('profile-success') }}
                </flux:callout>
            @endif

            <form wire:submit="updateProfile" class="space-y-4">
                <flux:field>
                    <flux:label>Full name</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email address</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </form>
        </flux:card>

        {{-- ── PAY CYCLE ────────────────────────────────────── --}}
        <flux:card>
            <div class="mb-5">
                <flux:heading size="sm">Pay cycle</flux:heading>
                <flux:text size="sm">
                    The day your salary lands. From this day onward, the app plans for the
                    <strong>upcoming month</strong> — so a paycheck arriving late this month funds next month's budget.
                </flux:text>
            </div>

            @if(session('paycycle-success'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    {{ session('paycycle-success') }}
                </flux:callout>
            @endif

            <form wire:submit="savePayCycle" class="space-y-4">
                <flux:field>
                    <flux:label>Pay cycle start day</flux:label>
                    <flux:input wire:model="pay_cycle_day" type="number" min="1" max="31" style="width:120px" />
                    <flux:error name="pay_cycle_day" />
                    <flux:text size="sm" class="text-slate-400 mt-1">
                        Day 1–31. For example, 25 means each budget month runs from the 25th of the
                        previous month to the 24th. If you pick a day a month doesn't have (e.g. 31 in
                        February), it falls to that month's last day.
                    </flux:text>
                </flux:field>

                <flux:button type="submit" variant="primary">Save pay cycle</flux:button>
            </form>
        </flux:card>

        {{-- ── STARTING BALANCE (carry-forward anchor) ──────── --}}
        <flux:card>
            <div class="mb-5">
                <flux:heading size="sm">Starting balance</flux:heading>
                <flux:text size="sm">
                    Your balance carries forward between months instead of resetting. Set what you had
                    saved on a given date, and every cycle since builds on it.
                </flux:text>
            </div>

            @if(session('openingbalance-success'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    {{ session('openingbalance-success') }}
                </flux:callout>
            @endif

            <form wire:submit="saveOpeningBalance" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Balance (KSh)</flux:label>
                        <flux:input wire:model="opening_balance" type="number" step="0.01" placeholder="0.00" />
                        <flux:error name="opening_balance" />
                    </flux:field>
                    <flux:field>
                        <flux:label>As of date <span class="text-slate-400">(optional)</span></flux:label>
                        <flux:input wire:model="opening_balance_at" type="date" />
                        <flux:error name="opening_balance_at" />
                    </flux:field>
                </div>
                <flux:text size="sm" class="text-slate-400">
                    Leave the date blank to anchor from your first transaction. Saving recalculates your carried-forward balance.
                </flux:text>
                <flux:button type="submit" variant="primary">Save starting balance</flux:button>
            </form>
        </flux:card>

        {{-- ── PASSWORD ─────────────────────────────────────── --}}
        <flux:card>
            <div class="mb-5">
                <flux:heading size="sm">Password</flux:heading>
                <flux:text size="sm">Use a strong password you don't reuse elsewhere</flux:text>
            </div>

            @if(session('password-success'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    {{ session('password-success') }}
                </flux:callout>
            @endif

            <form wire:submit="updatePassword" class="space-y-4">
                <flux:field>
                    <flux:label>Current password</flux:label>
                    <flux:input wire:model="current_password" type="password" viewable />
                    <flux:error name="current_password" />
                </flux:field>

                <flux:field>
                    <flux:label>New password</flux:label>
                    <flux:input wire:model="password" type="password" viewable />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirm new password</flux:label>
                    <flux:input wire:model="password_confirmation" type="password" viewable />
                </flux:field>

                <flux:button type="submit" variant="primary">Update password</flux:button>
            </form>
        </flux:card>

        {{-- ── PREFERENCES ──────────────────────────────────── --}}
        <flux:card>
            <div class="mb-5">
                <flux:heading size="sm">Appearance</flux:heading>
                <flux:text size="sm">Choose how Finance Manager looks to you</flux:text>
            </div>

            <div x-data class="inline-flex rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                <button type="button"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm transition-colors border-r border-slate-200 dark:border-slate-700"
                        :class="$flux.appearance === 'light'
                            ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                        x-on:click="$flux.appearance = 'light'">
                    <i class="ti ti-sun text-base leading-none"></i>
                    <span>Light</span>
                </button>
                <button type="button"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm transition-colors border-r border-slate-200 dark:border-slate-700"
                        :class="$flux.appearance === 'dark'
                            ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                        x-on:click="$flux.appearance = 'dark'">
                    <i class="ti ti-moon text-base leading-none"></i>
                    <span>Dark</span>
                </button>
                <button type="button"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm transition-colors"
                        :class="$flux.appearance === 'system'
                            ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-medium'
                            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                        x-on:click="$flux.appearance = 'system'">
                    <i class="ti ti-device-desktop text-base leading-none"></i>
                    <span>System</span>
                </button>
            </div>
        </flux:card>

        {{-- ── DANGER ───────────────────────────────────────── --}}
        <flux:card class="border-red-200 dark:border-red-600/30">
            <div class="mb-5">
                <flux:heading size="sm" class="text-red-600 dark:text-red-400">Delete account</flux:heading>
                <flux:text size="sm">
                    This permanently deletes your account and all associated data —
                    transactions, categories, and goals. This action cannot be undone.
                </flux:text>
            </div>

            <form wire:submit="deleteAccount" class="space-y-4">
                <flux:field>
                    <flux:label>Type <strong class="text-red-500">DELETE</strong> to confirm</flux:label>
                    <flux:input wire:model="delete_confirmation" placeholder="DELETE" />
                    <flux:error name="delete_confirmation" />
                </flux:field>

                <flux:button type="submit" variant="danger"
                             wire:confirm="Are you absolutely sure? This cannot be undone.">
                    Delete my account
                </flux:button>
            </form>
        </flux:card>

    </div>

</x-layouts.dashboard>
</div>
