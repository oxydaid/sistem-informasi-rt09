<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'name',
        'gender',
        'phone',
        'email',
        'avatar',
        'bio',
        'join_year',
        'status', // active, inactive, alumni
    ];

    protected function casts(): array
    {
        return [
            'join_year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PositionMemberPeriod::class);
    }
}
