<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'color', 'icon'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function defaults(): array
    {
        return [
            // Income
            ['name' => 'Salary',         'type' => 'income',  'color' => '#22c55e', 'icon' => 'ti-briefcase'],
            ['name' => 'Freelance',      'type' => 'income',  'color' => '#10b981', 'icon' => 'ti-device-laptop'],
            ['name' => 'Investment',     'type' => 'income',  'color' => '#06b6d4', 'icon' => 'ti-trending-up'],
            ['name' => 'Gift',           'type' => 'income',  'color' => '#a855f7', 'icon' => 'ti-gift'],
            // Expense
            ['name' => 'Housing',        'type' => 'expense', 'color' => '#dc2626', 'icon' => 'ti-home'],
            ['name' => 'Food',           'type' => 'expense', 'color' => '#f97316', 'icon' => 'ti-tools-kitchen-2'],
            ['name' => 'Transport',      'type' => 'expense', 'color' => '#eab308', 'icon' => 'ti-car'],
            ['name' => 'Utilities',      'type' => 'expense', 'color' => '#0ea5e9', 'icon' => 'ti-bolt'],
            ['name' => 'Entertainment',  'type' => 'expense', 'color' => '#8b5cf6', 'icon' => 'ti-device-tv'],
            ['name' => 'Healthcare',     'type' => 'expense', 'color' => '#ec4899', 'icon' => 'ti-heart'],
            ['name' => 'Shopping',       'type' => 'expense', 'color' => '#f59e0b', 'icon' => 'ti-shopping-bag'],
            ['name' => 'Education',      'type' => 'expense', 'color' => '#3b82f6', 'icon' => 'ti-school'],
            ['name' => 'Other',          'type' => 'expense', 'color' => '#64748b', 'icon' => 'ti-dots'],
        ];
    }

    public static function seedDefaultsFor(User $user): void
    {
        if ($user->categories()->exists()) return;

        foreach (self::defaults() as $cat) {
            $user->categories()->create($cat);
        }
    }
}
