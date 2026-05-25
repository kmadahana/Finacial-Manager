<?php

namespace App\Livewire\Goals;

use Livewire\Component;
use App\Models\Goal;

class Index extends Component
{
    public string $name = '';
    public string $target_amount  = '';
    public string $current_amount = '0';
    public string $target_date = '';
    public string $color = '#dc2626';
    public string $icon  = 'ti-target';
    public ?int $editing_id = null;
    public bool $showForm = false;

    public function mount(): void
    {
        $this->target_date = now()->addMonths(6)->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'target_amount'  => ['required', 'numeric', 'min:1'],
            'current_amount' => ['required', 'numeric', 'min:0'],
            'target_date'    => ['nullable', 'date'],
            'color'          => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon'           => ['required', 'string', 'max:30'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $g = Goal::where('user_id', auth()->id())->findOrFail($id);
        $this->editing_id     = $g->id;
        $this->name           = $g->name;
        $this->target_amount  = (string) $g->target_amount;
        $this->current_amount = (string) $g->current_amount;
        $this->target_date    = $g->target_date?->format('Y-m-d') ?? '';
        $this->color          = $g->color;
        $this->icon           = $g->icon;
        $this->showForm       = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['user_id'] = auth()->id();
        if (empty($data['target_date'])) $data['target_date'] = null;

        if ($this->editing_id) {
            Goal::where('user_id', auth()->id())
                ->where('id', $this->editing_id)
                ->update($data);
            session()->flash('success', 'Goal updated.');
        } else {
            Goal::create($data);
            session()->flash('success', 'Goal added.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Goal::where('user_id', auth()->id())->where('id', $id)->delete();
        session()->flash('success', 'Goal deleted.');
    }

    public function contribute(int $id, float $amount): void
    {
        $g = Goal::where('user_id', auth()->id())->findOrFail($id);
        $g->current_amount = (float) $g->current_amount + $amount;
        $g->save();
        session()->flash('success', 'Added KSh ' . number_format($amount, 2) . ' to ' . $g->name);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'target_amount', 'editing_id']);
        $this->current_amount = '0';
        $this->target_date    = now()->addMonths(6)->format('Y-m-d');
        $this->color          = '#dc2626';
        $this->icon           = 'ti-target';
        $this->resetErrorBag();
    }

    public function render()
    {
        $goals = auth()->user()->goals()->orderByDesc('id')->get();

        return view('livewire.goals.index', [
            'goals'        => $goals,
            'totalSaved'   => $goals->sum('current_amount'),
            'totalTarget'  => $goals->sum('target_amount'),
            'iconOptions'  => ['ti-target', 'ti-plane', 'ti-home', 'ti-car', 'ti-school',
                               'ti-heart', 'ti-shield', 'ti-device-laptop', 'ti-camera',
                               'ti-gift', 'ti-trophy', 'ti-wallet'],
            'colorOptions' => ['#dc2626', '#f97316', '#eab308', '#22c55e', '#10b981',
                               '#06b6d4', '#0ea5e9', '#3b82f6', '#8b5cf6', '#a855f7',
                               '#ec4899', '#64748b'],
        ])->layout('components.layouts.app');
    }
}
