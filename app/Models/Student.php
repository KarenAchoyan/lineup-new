<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'biradi',
        'course_id',
        'branch_id',
        'birthday',
        'attendance_days',
    ];

    protected $casts = [
        'birthday' => 'date',
        'attendance_days' => 'array',
    ];

    /**
     * Get the user (parent) that owns the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that the student is enrolled in.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the branch where the student studies.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the payments for the student.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * The groups that the student belongs to.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * Get the attendances for the student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if student has paid this month.
     */
    public function hasPaidThisMonth()
    {
        return $this->payments()
            ->where('status', 'success')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->exists();
    }
}
