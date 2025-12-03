<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_hy',
        'name_ge',
        'name_ru',
        'course_id',
        'teacher_id',
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
     * Get the course that the group belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher (user) that owns the group.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The students that belong to the group.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    /**
     * Get the attendances for the group.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
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
     * Scope for active groups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered groups
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name_en');
    }
}
