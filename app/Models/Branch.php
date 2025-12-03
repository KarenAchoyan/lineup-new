<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_hy',
        'name_ge',
        'name_ru',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get name based on locale
     */
    public function getName($locale = 'en')
    {
        return $this->{"name_{$locale}"} ?? $this->name_en ?? '';
    }

    /**
     * The courses that belong to the branch.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_branch');
    }

    /**
     * Scope for active branches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered branches
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name_en');
    }
}
