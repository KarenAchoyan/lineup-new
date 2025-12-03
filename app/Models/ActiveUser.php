<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname_en',
        'fullname_hy',
        'fullname_ge',
        'fullname_ru',
        'about_en',
        'about_hy',
        'about_ge',
        'about_ru',
        'avatar',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get fullname based on locale
     */
    public function getFullname($locale = 'en')
    {
        return $this->{"fullname_{$locale}"} ?? $this->fullname_en ?? '';
    }

    /**
     * Get about content based on locale
     */
    public function getAbout($locale = 'en')
    {
        return $this->{"about_{$locale}"} ?? $this->about_en ?? '';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered users
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('fullname_en');
    }
}
