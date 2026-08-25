<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'max_members',
        'sort_order',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'max_members' => 'integer',
            'sort_order' => 'integer',
            'is_default' => 'boolean',
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
