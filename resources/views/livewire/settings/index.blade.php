<div>
<x-layouts.dashboard title="Settings" subtitle="Manage your account">

    <div class="max-w-3xl space-y-6">

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
