<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';
    protected $fillable = [
        'fullname_en',
        'fullname_hy',
        'fullname_ge',
        'fullname_ru',
        'profession_en',
        'profession_hy',
        'profession_ge',
        'profession_ru',
        'phone',
        'email',
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
     * Get profession based on locale
     */
    public function getProfession($locale = 'en')
    {
        return $this->{"profession_{$locale}"} ?? $this->profession_en ?? '';
    }

    /**
     * Scope for active staff
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered staff
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('fullname_en');
    }
}
