<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Student;
use App\Models\User;
use App\Models\Payment;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $search = $request->input('search', '');
        $courseId = $request->input('course_id');

        $query = Group::with(['course', 'students.user'])->ordered();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_hy', 'like', "%{$search}%")
                  ->orWhere('name_ge', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
        }

        if (!empty($courseId)) {
            $query->where('course_id', $courseId);
        }

        $groups = $query->get()->map(function ($group) use ($locale) {
            return [
                'id' => $group->id,
                'name' => $group->getName($locale),
                'name_en' => $group->name_en,
                'name_hy' => $group->name_hy,
                'name_ge' => $group->name_ge,
                'name_ru' => $group->name_ru,
                'course_id' => $group->course_id,
                'course_name' => $group->course->getName($locale),
                'description' => $group->getDescription($locale),
                'order' => $group->order,
                'is_active' => $group->is_active,
                'students_count' => $group->students->count(),
                'students' => $group->students->map(function ($student) use ($locale) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'biradi' => $student->biradi,
                        'user_name' => $student->user->name,
                        'user_email' => $student->user->email,
                    ];
                }),
            ];
        });

        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        return Inertia::render('Admin/Groups/Index', [
            'groups' => $groups,
            'courses' => $courses,
            'filters' => [
                'search' => $search,
                'course_id' => $courseId,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locale = app()->getLocale();
        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        return Inertia::render('Admin/Groups/Create', [
            'courses' => $courses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
        ]);

        $students = $validated['students'] ?? [];
        unset($validated['students']);

        $group = Group::create($validated);
        $group->students()->sync($students);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $locale = app()->getLocale();
        $group->load(['course', 'students.user']);

        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        return Inertia::render('Admin/Groups/Edit', [
            'group' => [
                'id' => $group->id,
                'name_en' => $group->name_en,
                'name_hy' => $group->name_hy,
                'name_ge' => $group->name_ge,
                'name_ru' => $group->name_ru,
                'course_id' => $group->course_id,
                'description_en' => $group->description_en,
                'description_hy' => $group->description_hy,
                'description_ge' => $group->description_ge,
                'description_ru' => $group->description_ru,
                'order' => $group->order,
                'is_active' => $group->is_active,
                'students' => $group->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'biradi' => $student->biradi,
                        'user_id' => $student->user_id,
                        'user_name' => $student->user->name,
                        'user_email' => $student->user->email,
                    ];
                }),
            ],
            'courses' => $courses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
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
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
        ]);

        $students = $validated['students'] ?? [];
        unset($validated['students']);

        $group->update($validated);
        $group->students()->sync($students);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    /**
     * Search for students by parent user (for adding to group)
     */
    public function searchStudents(Request $request)
    {
        $search = $request->input('search', '');
        $locale = app()->getLocale();

        if (empty($search)) {
            return response()->json(['students' => []]);
        }

        // Search by parent user name or email, student name, or biradi
        $students = Student::with(['user', 'course', 'branch'])
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('biradi', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
            })
            ->limit(20)
            ->get()
            ->map(function ($student) use ($locale) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'biradi' => $student->biradi,
                    'user_id' => $student->user_id,
                    'user_name' => $student->user->name,
                    'user_email' => $student->user->email,
                    'course_name' => $student->course->getName($locale),
                    'branch_name' => $student->branch->getName($locale),
                ];
            });

        return response()->json(['students' => $students]);
    }

    /**
     * Show students of a specific group with filters
     */
    public function showStudents(Request $request, Group $group)
    {
        $locale = app()->getLocale();
        $search = $request->input('search', '');
        $courseId = $request->input('course_id');
        $branchId = $request->input('branch_id');

        $query = $group->students()->with(['user', 'course', 'branch']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('biradi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($courseId)) {
            $query->where('course_id', $courseId);
        }

        if (!empty($branchId)) {
            $query->where('branch_id', $branchId);
        }

        $students = $query->get()->map(function ($student) use ($locale, $group) {
            // Check if student has paid for this specific group this month
            $hasPaidThisMonth = Payment::where('student_id', $student->id)
                ->where('group_id', $group->id)
                ->where('status', 'success')
                ->whereYear('paid_at', Carbon::now()->year)
                ->whereMonth('paid_at', Carbon::now()->month)
                ->exists();

            return [
                'id' => $student->id,
                'name' => $student->name,
                'biradi' => $student->biradi,
                'user_id' => $student->user_id,
                'user_name' => $student->user->name,
                'user_email' => $student->user->email,
                'course_id' => $student->course_id,
                'branch_id' => $student->branch_id,
                'course_name' => $student->course->getName($locale),
                'branch_name' => $student->branch->getName($locale),
                'has_paid_this_month' => $hasPaidThisMonth,
            ];
        });

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

        // Get messages sent to users who have students in this group
        $groupStudentIds = $group->students()->pluck('students.id')->toArray();
        $groupUserIds = $group->students()->pluck('students.user_id')->unique()->toArray();

        $messages = Message::whereNull('parent_message_id')
            ->where(function ($query) use ($groupStudentIds, $groupUserIds) {
                // Messages sent to users in this group
                $query->whereIn('to_user_id', $groupUserIds)
                      ->orWhere(function ($q) use ($groupStudentIds) {
                          // Or messages that include students from this group
                          foreach ($groupStudentIds as $studentId) {
                              $q->orWhereJsonContains('student_ids', $studentId);
                          }
                      });
            })
            ->with(['fromUser', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10) // Limit to last 10 messages
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'message' => \Illuminate\Support\Str::limit($message->message, 150),
                    'from_user_name' => $message->fromUser->name,
                    'to_user_name' => $message->toUser->name,
                    'student_ids' => $message->student_ids ?? [],
                    'has_students' => !empty($message->student_ids),
                    'is_read' => $message->isRead(),
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $message->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'group' => [
                'id' => $group->id,
                'name' => $group->getName($locale),
            ],
            'students' => $students,
            'courses' => $courses,
            'branches' => $branches,
            'messages' => $messages,
            'filters' => [
                'search' => $search,
                'course_id' => $courseId,
                'branch_id' => $branchId,
            ],
        ]);
    }
}
