<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $role // super_admin, admin_rt, admin_karang_taruna
 * @property int|null $organization_id
 * @property string|null $phone
 * @property bool $is_active
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'organization_id',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function memberProfile(): HasOne
    {
        return $this->hasOne(OrganizationMember::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'author_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminRt(): bool
    {
        return $this->role === 'admin_rt' || $this->role === 'super_admin';
    }

    public function isAdminKarangTaruna(): bool
    {
        return $this->role === 'admin_karang_taruna' || $this->role === 'super_admin';
    }

    public function canAccessOrganization(?int $organizationId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->organization_id !== null && (int) $this->organization_id === (int) $organizationId;
    }
}
