<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'start_year',
        'end_year',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_year' => 'integer',
            'end_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function memberPositions(): HasMany
    {
        return $this->hasMany(PositionMemberPeriod::class);
    }
}
