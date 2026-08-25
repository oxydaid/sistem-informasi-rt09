<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'type', // announcement, article, event, activity, page
        'title',
        'slug',
        'excerpt',
        'content', // JSON
        'content_html',
        'thumbnail',
        'status', // draft, published, archived
        'published_at',
        'author_id',
        'view_count',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    public function activity(): HasOne
    {
        return $this->hasOne(Activity::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ContentAttachment::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
