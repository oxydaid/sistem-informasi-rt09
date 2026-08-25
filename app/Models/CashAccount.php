<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'account_number',
        'current_balance',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }
}
