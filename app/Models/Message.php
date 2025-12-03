<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'parent_message_id',
        'subject',
        'message',
        'student_ids',
        'read_at',
    ];

    protected $casts = [
        'student_ids' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message.
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who received the message.
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Get the students associated with this message.
     */
    public function getStudentsAttribute()
    {
        if (empty($this->student_ids)) {
            return collect();
        }
        return Student::whereIn('id', $this->student_ids)->get();
    }

    /**
     * Check if message is read.
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Get the parent message (if this is a reply).
     */
    public function parentMessage()
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    /**
     * Get all replies to this message.
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_message_id')->orderBy('created_at', 'asc');
    }

    /**
     * Check if this message is a reply.
     */
    public function isReply()
    {
        return !is_null($this->parent_message_id);
    }
}
