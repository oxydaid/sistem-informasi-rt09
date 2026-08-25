<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'activity_date',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(ActivityMedia::class)->orderBy('sort_order');
    }
}
