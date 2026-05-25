<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class Register extends Component
{
    public int $step = 1;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $otp = '';

    public function register()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Category::seedDefaultsFor($user);

        $this->sendOtp($user);
        $this->step = 2;
    }

    protected function sendOtp(User $user): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'            => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($code, 'Verify your email'));
    }

    public function resendOtp()
    {
        $user = User::where('email', $this->email)->first();
        if ($user) {
            $this->sendOtp($user);
            session()->flash('status', 'A new code was sent to your email.');
        }
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|digits:6']);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('otp', 'Something went wrong. Please register again.');
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
            'otp'               => null,
            'otp_expires_at'    => null,
            'email_verified_at' => now(),
        ]);

        session()->flash('status', 'Account verified! Please log in to continue.');
$this->redirect(route('login'));
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.app');
    }
}