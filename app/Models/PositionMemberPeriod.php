<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionMemberPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_member_id',
        'organization_position_id',
        'organization_period_id',
        'notes',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class, 'organization_member_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(OrganizationPosition::class, 'organization_position_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(OrganizationPeriod::class, 'organization_period_id');
    }
}
