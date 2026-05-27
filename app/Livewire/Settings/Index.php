<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

#[\Livewire\Attributes\Title('Settings')]
class Index extends Component
{
    use WithFileUploads;

    // Profile
    public string $name  = '';
    public string $email = '';

    // Pay cycle
    public int $pay_cycle_day = 25;

    // Carry-forward starting balance
    public string $opening_balance = '0';
    public ?string $opening_balance_at = null;

    // Avatar
    public $avatar = null;

    // Password
    public string $current_password      = '';
    public string $password              = '';
    public string $password_confirmation = '';

    // Delete account
    public string $delete_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->pay_cycle_day = (int) ($user->pay_cycle_day ?? 25);
        $this->opening_balance = (string) ($user->opening_balance ?? '0');
        $this->opening_balance_at = $user->opening_balance_at?->format('Y-m-d');
    }

    public function savePayCycle(): void
    {
        $this->validate([
            'pay_cycle_day' => ['required', 'integer', 'min:1', 'max:31'],
        ], [
            'pay_cycle_day.min' => 'Pick a day between 1 and 31.',
            'pay_cycle_day.max' => 'Pick a day between 1 and 31.',
        ]);

        auth()->user()->update(['pay_cycle_day' => $this->pay_cycle_day]);

        session()->flash('paycycle-success', 'Pay cycle updated.');
    }

    public function saveOpeningBalance(): void
    {
        $this->validate([
            'opening_balance'    => ['required', 'numeric'],
            'opening_balance_at' => ['nullable', 'date'],
        ], [
            'opening_balance.numeric' => 'Enter a valid amount.',
        ]);

        auth()->user()->update([
            'opening_balance'    => (float) $this->opening_balance,
            'opening_balance_at' => $this->opening_balance_at ?: null,
        ]);

        // Re-materialise the carry-forward ledger from the new anchor.
        \App\Models\CycleSummary::where('user_id', auth()->id())->delete();
        \App\Support\CycleLedger::sync(auth()->user()->fresh());

        session()->flash('openingbalance-success', 'Starting balance updated.');
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('profile-success', 'Profile updated.');
    }

    public function uploadAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $this->avatar->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        $this->avatar = null;
        session()->flash('avatar-success', 'Avatar updated.');
        $this->redirect(route('settings'));
    }

    public function removeAvatar(): void
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        session()->flash('avatar-success', 'Avatar removed.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password-success', 'Password updated.');
    }

    public function deleteAccount(): void
    {
        $this->validate([
            'delete_confirmation' => ['required', 'in:DELETE'],
        ], [
            'delete_confirmation.in' => 'Type DELETE in capital letters to confirm.',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect('/');
    }

    public function render()
    {
        return view('livewire.settings.index')
            ->layout('components.layouts.app');
    }
}
