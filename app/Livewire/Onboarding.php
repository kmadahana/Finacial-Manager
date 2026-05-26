<?php

namespace App\Livewire;

use Livewire\Component;

class Onboarding extends Component
{
    public bool $show = false;
    public int $step = 1;
    public int $pay_cycle_day = 25;

    public function mount(): void
    {
        $user = auth()->user();
        $this->show = $user && is_null($user->onboarded_at);
        $this->pay_cycle_day = (int) ($user->pay_cycle_day ?? 25);
    }

    public function next(): void
    {
        $this->step = min(3, $this->step + 1);
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
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

        $this->step = 3;
    }

    public function finish(): void
    {
        auth()->user()->update(['onboarded_at' => now()]);
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.onboarding');
    }
}
