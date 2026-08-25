<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'media_id',
        'display_name',
        'sort_order',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
