<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = [
        'user_id', 'name', 'target_amount', 'current_amount', 'target_date', 'color', 'icon',
    ];

    protected function casts(): array
    {
        return [
            'target_date'    => 'date',
            'target_amount'  => 'decimal:2',
            'current_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentAttribute(): float
    {
        if ((float) $this->target_amount <= 0) return 0;
        return min(100, round(((float) $this->current_amount / (float) $this->target_amount) * 100, 1));
    }
}
