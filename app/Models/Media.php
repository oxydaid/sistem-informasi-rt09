<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk',
        'file_path',
        'original_name',
        'file_name',
        'mime_type',
        'file_size',
        'width',
        'height',
        'alt_text',
        'caption',
        'uploaded_by_user_id',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk ?? 'public')->url($this->file_path);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function activityMedia(): HasMany
    {
        return $this->hasMany(ActivityMedia::class);
    }

    public function contentAttachments(): HasMany
    {
        return $this->hasMany(ContentAttachment::class);
    }
}
