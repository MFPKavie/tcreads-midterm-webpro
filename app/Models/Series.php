<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Series extends Model
{
    protected $fillable = [
        'title', 'author', 'media_type', 'current_chapter', 'total_chapters',
        'status', 'rating', 'is_favorite', 'review_notes',
    ];

    protected function casts(): array
    {
        return ['is_favorite' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completionPercentage(): ?int
    {
        if (!$this->total_chapters) {
            return null;
        }

        return (int) round(($this->current_chapter / $this->total_chapters) * 100);
    }
}
