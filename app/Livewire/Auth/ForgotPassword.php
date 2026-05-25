<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class ForgotPassword extends Component
{
    public int $step = 1;
    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function sendOtp()
    {
        $this->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No account found with that email address.',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        User::where('email', $this->email)->update([
            'otp'            => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($this->email)->send(new OtpMail($code, 'Reset your password'));
        $this->step = 2;
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
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        $this->step = 3;
    }

    public function resendOtp()
    {
        $this->otp = '';
        $this->sendOtp();
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($this->step !== 3) {
            $this->addError('password', 'Please verify your OTP first.');
            return;
        }

        $user = User::where('email', $this->email)->first();

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        session()->flash('status', 'Password reset successfully!');
        $this->redirect(route('login'));
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.app');
    }
}