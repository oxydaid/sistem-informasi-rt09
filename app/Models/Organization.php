<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type', // 'rt' or 'karang_taruna'
        'code',
        'description',
        'address',
        'phone',
        'email',
        'logo',
        'cover_image',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    public function periods(): HasMany
    {
        return $this->hasMany(OrganizationPeriod::class);
    }

    public function activePeriod(): HasOne
    {
        return $this->hasOne(OrganizationPeriod::class)->where('is_active', true);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(OrganizationPosition::class)->orderBy('sort_order');
    }

    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function cashAccounts(): HasMany
    {
        return $this->hasMany(CashAccount::class);
    }

    public function cashCategories(): HasMany
    {
        return $this->hasMany(CashCategory::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
