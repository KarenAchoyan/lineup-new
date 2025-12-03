<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_date',
        'content_en',
        'content_hy',
        'content_ge',
        'content_ru',
        'avatar',
        'slug',
        'is_active',
        'order',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug('event-' . $event->event_date . '-' . time());
            }
        });
    }

    /**
     * Get content based on locale
     */
    public function getContent($locale = 'en')
    {
        return $this->{"content_{$locale}"} ?? $this->content_en ?? '';
    }

    /**
     * Scope for active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered events
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('event_date', 'asc')->orderBy('order');
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }
}
