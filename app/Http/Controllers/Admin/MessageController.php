<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of conversations.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get root messages (not replies) where admin is sender or recipient
        $messages = Message::whereNull('parent_message_id')
            ->where(function ($query) use ($user) {
                $query->where('from_user_id', $user->id)
                      ->orWhere('to_user_id', $user->id);
            })
            ->with(['fromUser', 'toUser', 'replies'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($message) use ($user) {
                $repliesCount = $message->replies->count();
                $unreadReplies = $message->replies->filter(function ($reply) use ($user) {
                    return $reply->to_user_id === $user->id && !$reply->isRead();
                })->count();
                
                // Get the other user in the conversation
                $otherUser = $message->from_user_id === $user->id 
                    ? $message->toUser 
                    : $message->fromUser;
                
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'other_user_id' => $otherUser->id,
                    'other_user_name' => $otherUser->name,
                    'other_user_email' => $otherUser->email,
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
                    'updated_at_human' => $message->updated_at->diffForHumans(),
                ];
            });

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages,
        ]);
    }

    /**
     * Display the specified conversation.
     */
    public function show(Request $request, Message $message)
    {
        $user = $request->user();
        
        // Get the root message
        $rootMessage = $message->parentMessage ? $message->parentMessage : $message;
        
        // Ensure the root message belongs to admin
        if ($rootMessage->from_user_id !== $user->id && $rootMessage->to_user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Mark root message as read if admin is the recipient
        if ($rootMessage->to_user_id === $user->id) {
            $rootMessage->markAsRead();
        }

        $locale = app()->getLocale();
        
        // Load replies
        $rootMessage->load(['replies.fromUser', 'replies.toUser']);
        
        // Get the other user in the conversation
        $otherUser = $rootMessage->from_user_id === $user->id 
            ? $rootMessage->toUser 
            : $rootMessage->fromUser;
        
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

        return Inertia::render('Admin/Messages/Show', [
            'rootMessage' => [
                'id' => $rootMessage->id,
                'subject' => $rootMessage->subject,
                'student_ids' => $rootMessage->student_ids ?? [],
                'has_students' => !empty($rootMessage->student_ids),
            ],
            'otherUser' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'email' => $otherUser->email,
            ],
            'messages' => $messages,
            'students' => $students,
            'currentUserId' => $user->id,
        ]);
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        $locale = app()->getLocale();
        
        // Get all teachers
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('slug', 'teacher');
        })->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
            ];
        });

        // Get all users with their students
        $users = User::with(['students.course', 'students.branch'])
            ->whereHas('students')
            ->get()
            ->map(function ($user) use ($locale) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'students' => $user->students->map(function ($student) use ($locale) {
                        return [
                            'id' => $student->id,
                            'name' => $student->name,
                            'biradi' => $student->biradi,
                            'course_id' => $student->course_id,
                            'branch_id' => $student->branch_id,
                            'course_name' => $student->course->getName($locale),
                            'branch_name' => $student->branch->getName($locale),
                        ];
                    }),
                ];
            });

        // Get courses and branches for filters
        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        $branches = Branch::active()->ordered()->get()->map(function ($branch) use ($locale) {
            return [
                'id' => $branch->id,
                'name' => $branch->getName($locale),
            ];
        });

        return Inertia::render('Admin/Messages/Create', [
            'teachers' => $teachers,
            'users' => $users,
            'courses' => $courses,
            'branches' => $branches,
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        // Verify the recipient is a teacher
        $teacher = User::findOrFail($validated['to_user_id']);
        if (!$teacher->hasRole('teacher')) {
            return back()->withErrors(['to_user_id' => 'The selected user is not a teacher.']);
        }

        $message = Message::create([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $validated['to_user_id'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'student_ids' => $validated['student_ids'] ?? [],
        ]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $user = $request->user();
        
        // Get the root message
        $rootMessage = $message->parentMessage ? $message->parentMessage : $message;
        
        // Ensure the root message belongs to admin
        if ($rootMessage->from_user_id !== $user->id && $rootMessage->to_user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Determine recipient (if admin sent it, reply to teacher; if admin received it, reply to teacher)
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

        return redirect()->route('admin.messages.show', $rootMessage->id)
            ->with('success', 'Reply sent successfully.');
    }

    /**
     * Send message to multiple users (parents).
     */
    public function sendToUsers(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $admin = $request->user();
        $sentCount = 0;

        foreach ($validated['user_ids'] as $userId) {
            Message::create([
                'from_user_id' => $admin->id,
                'to_user_id' => $userId,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'student_ids' => null, // No specific students for payment reminders
            ]);
            $sentCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Message sent successfully to {$sentCount} user(s).",
            'sent_count' => $sentCount,
        ]);
    }
}
