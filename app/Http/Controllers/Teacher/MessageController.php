<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Group;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of messages.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get root messages (not replies) where teacher is recipient or sender
        $messages = Message::whereNull('parent_message_id')
            ->where(function ($query) use ($user) {
                $query->where('to_user_id', $user->id)
                      ->orWhere('from_user_id', $user->id);
            })
            ->with(['fromUser', 'toUser', 'replies'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) use ($user) {
                $repliesCount = $message->replies->count();
                $unreadReplies = $message->replies->filter(function ($reply) use ($user) {
                    return $reply->to_user_id === $user->id && !$reply->isRead();
                })->count();
                
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'from_user_name' => $message->fromUser->name,
                    'from_user_email' => $message->fromUser->email,
                    'to_user_name' => $message->toUser->name,
                    'to_user_email' => $message->toUser->email,
                    'is_from_me' => $message->from_user_id === $user->id,
                    'student_ids' => $message->student_ids ?? [],
                    'has_students' => !empty($message->student_ids),
                    'is_read' => $message->isRead(),
                    'replies_count' => $repliesCount,
                    'unread_replies' => $unreadReplies,
                    'has_unread' => $unreadReplies > 0 || ($message->to_user_id === $user->id && !$message->isRead()),
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $message->created_at->diffForHumans(),
                    'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Teacher/Messages/Index', [
            'messages' => $messages,
        ]);
    }

    /**
     * Display the specified message.
     */
    public function show(Request $request, Message $message)
    {
        $user = $request->user();
        
        // Get the root message (if this is a reply, get the original)
        $rootMessage = $message->parentMessage ? $message->parentMessage : $message;
        
        // Ensure the root message belongs to the teacher
        if ($rootMessage->to_user_id !== $user->id && $rootMessage->from_user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Mark root message as read if teacher is the recipient
        if ($rootMessage->to_user_id === $user->id) {
            $rootMessage->markAsRead();
        }

        $locale = app()->getLocale();
        
        // Load replies
        $rootMessage->load(['replies.fromUser', 'replies.toUser']);
        
        // Get students if any (only from root message)
        $students = [];
        if (!empty($rootMessage->student_ids)) {
            $students = Student::whereIn('id', $rootMessage->student_ids)
                ->with(['user', 'course', 'branch'])
                ->get()
                ->map(function ($student) use ($locale) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'biradi' => $student->biradi,
                        'user_name' => $student->user->name,
                        'user_email' => $student->user->email,
                        'course_name' => $student->course->getName($locale),
                        'branch_name' => $student->branch->getName($locale),
                    ];
                });
        }

        // Get courses for group creation
        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        // Format messages for display
        $messages = collect([$rootMessage])->concat($rootMessage->replies)->map(function ($msg) {
            return [
                'id' => $msg->id,
                'subject' => $msg->subject,
                'message' => $msg->message,
                'from_user_id' => $msg->from_user_id,
                'from_user_name' => $msg->fromUser->name,
                'from_user_email' => $msg->fromUser->email,
                'to_user_id' => $msg->to_user_id,
                'to_user_name' => $msg->toUser->name,
                'to_user_email' => $msg->toUser->email,
                'is_reply' => $msg->isReply(),
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $msg->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Teacher/Messages/Show', [
            'rootMessage' => [
                'id' => $rootMessage->id,
                'subject' => $rootMessage->subject,
                'student_ids' => $rootMessage->student_ids ?? [],
                'has_students' => !empty($rootMessage->student_ids),
            ],
            'messages' => $messages,
            'students' => $students,
            'courses' => $courses,
            'currentUserId' => $user->id,
        ]);
    }

    /**
     * Create a group from message students.
     */
    public function createGroupFromMessage(Request $request, Message $message)
    {
        $user = $request->user();
        
        // Get the root message
        $rootMessage = $message->parentMessage ? $message->parentMessage : $message;
        
        // Ensure the root message belongs to the teacher
        if ($rootMessage->to_user_id !== $user->id && $rootMessage->from_user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (empty($rootMessage->student_ids)) {
            return back()->withErrors(['error' => 'No students in this message.']);
        }

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_hy' => 'required|string|max:255',
            'name_ge' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'description_en' => 'nullable|string',
            'description_hy' => 'nullable|string',
            'description_ge' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Create the group
        $group = Group::create([
            'teacher_id' => $user->id,
            'course_id' => $validated['course_id'],
            'name_en' => $validated['name_en'],
            'name_hy' => $validated['name_hy'],
            'name_ge' => $validated['name_ge'],
            'name_ru' => $validated['name_ru'],
            'description_en' => $validated['description_en'] ?? null,
            'description_hy' => $validated['description_hy'] ?? null,
            'description_ge' => $validated['description_ge'] ?? null,
            'description_ru' => $validated['description_ru'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Attach students from root message
        $group->students()->sync($rootMessage->student_ids);

        return redirect()->route('teacher.groups.index')
            ->with('success', 'Group created successfully from message.');
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $user = $request->user();
        
        // Get the root message
        $rootMessage = $message->parentMessage ? $message->parentMessage : $message;
        
        // Ensure the root message belongs to the teacher
        if ($rootMessage->to_user_id !== $user->id && $rootMessage->from_user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Determine recipient (if teacher received it, reply to admin; if teacher sent it, reply to teacher)
        $recipientId = $rootMessage->from_user_id === $user->id 
            ? $rootMessage->to_user_id 
            : $rootMessage->from_user_id;

        $reply = Message::create([
            'from_user_id' => $user->id,
            'to_user_id' => $recipientId,
            'parent_message_id' => $rootMessage->id,
            'subject' => 'Re: ' . $rootMessage->subject,
            'message' => $validated['message'],
            'student_ids' => null, // Replies don't include students
        ]);

        return redirect()->route('teacher.messages.show', $rootMessage->id)
            ->with('success', 'Reply sent successfully.');
    }
}
