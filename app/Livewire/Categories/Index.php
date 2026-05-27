<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;

#[\Livewire\Attributes\Title('Categories')]
class Index extends Component
{
    public string $name = '';
    public string $type = 'expense';
    public string $color = '#dc2626';
    public string $icon = 'ti-tag';
    public ?int $editing_id = null;
    public bool $showForm = false;

    public function mount(): void
    {
        Category::seedDefaultsFor(auth()->user());
    }

    protected function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:50'],
            'type'  => ['required', 'in:income,expense'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon'  => ['required', 'string', 'max:30'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $cat = Category::where('user_id', auth()->id())->findOrFail($id);
        $this->editing_id = $cat->id;
        $this->name       = $cat->name;
        $this->type       = $cat->type;
        $this->color      = $cat->color;
        $this->icon       = $cat->icon;
        $this->showForm   = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['user_id'] = auth()->id();

        if ($this->editing_id) {
            Category::where('user_id', auth()->id())
                ->where('id', $this->editing_id)
                ->update($data);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create($data);
            session()->flash('success', 'Category added.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Category::where('user_id', auth()->id())->where('id', $id)->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editing_id']);
        $this->type  = 'expense';
        $this->color = '#dc2626';
        $this->icon  = 'ti-tag';
        $this->resetErrorBag();
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.categories.index', [
            'incomeCategories'  => $user->categories()->where('type', 'income')->withCount('transactions')->orderBy('name')->get(),
            'expenseCategories' => $user->categories()->where('type', 'expense')->withCount('transactions')->orderBy('name')->get(),
            'iconOptions' => [
                'ti-tag', 'ti-home', 'ti-car', 'ti-bolt', 'ti-device-tv', 'ti-heart',
                'ti-shopping-bag', 'ti-school', 'ti-tools-kitchen-2', 'ti-briefcase',
                'ti-device-laptop', 'ti-trending-up', 'ti-gift', 'ti-plane',
                'ti-coffee', 'ti-paw', 'ti-book', 'ti-music', 'ti-shirt', 'ti-dots',
            ],
            'colorOptions' => [
                '#dc2626', '#f97316', '#eab308', '#22c55e', '#10b981', '#06b6d4',
                '#0ea5e9', '#3b82f6', '#8b5cf6', '#a855f7', '#ec4899', '#64748b',
            ],
        ])->layout('components.layouts.app');
    }
}
