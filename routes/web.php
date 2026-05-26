<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('login',           \App\Livewire\Auth\Login::class)->name('login');
    Route::get('register',        \App\Livewire\Auth\Register::class)->name('register');
    Route::get('forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('verify-email',    \App\Livewire\Auth\VerifyEmail::class)->name('verify.email');
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('income',    \App\Livewire\Income\Index::class)->name('income');

    Route::get('transactions',     \App\Livewire\Transactions\Index::class)->name('transactions');
    Route::get('transactions/all', \App\Livewire\Transactions\All::class)->name('transactions.all');
    Route::get('categories',       \App\Livewire\Categories\Index::class)->name('categories');
    Route::get('goals',        \App\Livewire\Goals\Index::class)->name('goals');
    Route::get('reports',      \App\Livewire\Reports\Index::class)->name('reports');
    Route::get('settings',     \App\Livewire\Settings\Index::class)->name('settings');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
