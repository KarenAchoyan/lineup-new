<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_hy',
        'title_ge',
        'title_ru',
        'content_en',
        'content_hy',
        'content_ge',
        'content_ru',
        'avatar',
        'video',
        'slug',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title_en ?? 'news-' . time());
            }
        });
    }

    /**
     * Get title based on locale
     */
    public function getTitle($locale = 'en')
    {
        return $this->{"title_{$locale}"} ?? $this->title_en ?? '';
    }

    /**
     * Get content based on locale
     */
    public function getContent($locale = 'en')
    {
        return $this->{"content_{$locale}"} ?? $this->content_en ?? '';
    }

    /**
     * Get YouTube video ID from URL
     */
    public function getYoutubeIdAttribute()
    {
        if (!$this->video) {
            return null;
        }

        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Scope for active news
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered news
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for latest news
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
