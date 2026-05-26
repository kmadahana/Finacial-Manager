<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CycleSummary extends Model
{
    protected $fillable = [
        'user_id', 'year', 'month',
        'opening_balance', 'income', 'expenses', 'closing_balance',
        'salary_snapshot', 'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'year'            => 'integer',
            'month'           => 'integer',
            'opening_balance' => 'decimal:2',
            'income'          => 'decimal:2',
            'expenses'        => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'salary_snapshot' => 'decimal:2',
            'finalized_at'    => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
