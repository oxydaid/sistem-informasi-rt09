<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'start_at',
        'end_at',
        'location',
        'maps_url',
        'is_online',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
