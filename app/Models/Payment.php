<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
        'order_id',
        'payment_id',
        'amount',
        'currency',
        'status',
        'payment_type',
        'payment_category',
        'order_desc',
        'paid_at',
        'flitt_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'flitt_response' => 'array',
    ];

    /**
     * Get the student that owns the payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the group that the payment is for.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Check if payment is successful.
     */
    public function isSuccessful()
    {
        return $this->status === 'success';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Scope for successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

