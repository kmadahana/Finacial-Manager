<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

#[\Livewire\Attributes\Title('Verify email')]
class VerifyEmail extends Component
{
    public string $otp = '';
    public bool $verified = false;
    public string $email = '';

    public function mount()
    {
        $this->email = session('registration_email', '');
        if (!$this->email) {
            $this->redirect(route('register'));
        }
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => ['required', 'digits:6']]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('otp', 'Something went wrong. Please try again.');
            return;
        }

        if (!$user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            $this->addError('otp', 'This code has expired. Please request a new one.');
            return;
        }

        if (!Hash::check($this->otp, $user->otp)) {
            $this->addError('otp', 'Invalid code. Please try again.');
            return;
        }

        $user->update([
            'email_verified_at' => now(),
            'otp'               => null,
            'otp_expires_at'    => null,
        ]);

        Auth::login($user);
        $this->verified = true;
    }

    public function resendOtp()
    {
        $user = User::where('email', $this->email)->first();
        if (!$user) return;

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp'            => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
        ]);
        Mail::to($this->email)->send(new OtpMail($code, 'Verify your email'));

        session()->flash('status', 'A new code has been sent to your email.');
    }

    public function render()
    {
        return view('livewire.auth.email-verify')
            ->layout('components.layouts.app');
    }
}
