<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_hy',
        'name_ge',
        'name_ru',
        'slug',
        'price',
        'months',
        'background_image',
        'description_en',
        'description_hy',
        'description_ge',
        'description_ru',
        'is_active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'months' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->name_en);
            }
        });
    }

    /**
     * Get name based on locale
     */
    public function getName($locale = 'en')
    {
        return $this->{"name_{$locale}"} ?? $this->name_en ?? '';
    }

    /**
     * Get description based on locale
     */
    public function getDescription($locale = 'en')
    {
        return $this->{"description_{$locale}"} ?? $this->description_en ?? '';
    }

    /**
     * The branches that belong to the course.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'course_branch')->orderBy('order');
    }

    /**
     * Scope for active courses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered courses
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name_en');
    }
}
