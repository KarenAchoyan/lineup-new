<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'year',
        'image',
        'youtube_link',
        'link',
        'title_en',
        'title_hy',
        'title_ge',
        'title_ru',
        'description_en',
        'description_hy',
        'description_ge',
        'description_ru',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get YouTube video ID from URL
     */
    public function getYoutubeIdAttribute()
    {
        if (!$this->youtube_link) {
            return null;
        }

        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->youtube_link, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Get YouTube embed URL
     */
    public function getYoutubeEmbedUrlAttribute()
    {
        if ($id = $this->youtube_id) {
            return "https://www.youtube.com/embed/{$id}";
        }
        return null;
    }

    /**
     * Get title based on locale
     */
    public function getTitle($locale = 'en')
    {
        return $this->{"title_{$locale}"} ?? $this->title_en ?? '';
    }

    /**
     * Get description based on locale
     */
    public function getDescription($locale = 'en')
    {
        return $this->{"description_{$locale}"} ?? $this->description_en ?? '';
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }
}
