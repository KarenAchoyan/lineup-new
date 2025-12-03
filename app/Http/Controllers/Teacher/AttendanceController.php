<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Display a listing of teacher's groups for attendance selection.
     */
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $user = $request->user();
        
        // Get filter parameters
        $search = $request->input('search', '');
        $courseId = $request->input('course_id');
        $branchId = $request->input('branch_id');

        $query = Group::where('teacher_id', $user->id)->active()->ordered()->with('course');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_hy', 'like', "%{$search}%")
                  ->orWhere('name_ge', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
        }

        // Filter by course
        if (!empty($courseId)) {
            $query->where('course_id', $courseId);
        }

        // Filter by branch (groups that have students from this branch)
        if (!empty($branchId)) {
            $query->whereHas('students', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $groups = $query->get()->map(function ($group) use ($locale) {
            return [
                'id' => $group->id,
                'name' => $group->getName($locale),
                'course_id' => $group->course_id,
                'course_name' => $group->course->getName($locale),
                'students_count' => $group->students()->count(),
            ];
        });

        // Get courses and branches for filter dropdowns
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

        return Inertia::render('Teacher/Attendance/Index', [
            'groups' => $groups,
            'courses' => $courses,
            'branches' => $branches,
            'filters' => [
                'search' => $search,
                'course_id' => $courseId,
                'branch_id' => $branchId,
            ],
        ]);
    }

    /**
     * Show attendance management for a specific group.
     */
    public function show(Request $request, Group $group)
    {
        // Ensure the group belongs to the teacher
        if ($group->teacher_id !== $request->user()->id) {
            abort(403, 'You can only manage attendance for your own groups.');
        }

        $locale = app()->getLocale();
        
        // Get date parameter or default to today
        $date = $request->input('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);
        
        // Get week start and end (Monday to Sunday)
        $weekStart = $selectedDate->copy()->startOfWeek();
        $weekEnd = $selectedDate->copy()->endOfWeek();
        
        // Get students in the group
        $students = $group->students()->with(['user', 'course', 'branch'])->get()->map(function ($student) use ($locale, $group, $weekStart, $weekEnd, $selectedDate) {
            // Get attendance for the week
            $attendances = Attendance::where('student_id', $student->id)
                ->where('group_id', $group->id)
                ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                ->get()
                ->keyBy(function ($attendance) {
                    return $attendance->date->format('Y-m-d');
                });

            // Calculate statistics
            $presentCount = Attendance::where('student_id', $student->id)
                ->where('group_id', $group->id)
                ->where('status', 'present')
                ->whereMonth('date', $selectedDate->month)
                ->whereYear('date', $selectedDate->year)
                ->count();

            $absentCount = Attendance::where('student_id', $student->id)
                ->where('group_id', $group->id)
                ->where('status', 'absent')
                ->whereMonth('date', $selectedDate->month)
                ->whereYear('date', $selectedDate->year)
                ->count();

            return [
                'id' => $student->id,
                'name' => $student->name,
                'biradi' => $student->biradi,
                'user_name' => $student->user->name,
                'user_email' => $student->user->email,
                'course_name' => $student->course->getName($locale),
                'branch_name' => $student->branch->getName($locale),
                'attendance_days' => $student->attendance_days ?? [],
                'attendances' => $attendances->map(function ($attendance) {
                    return [
                        'date' => $attendance->date->format('Y-m-d'),
                        'status' => $attendance->status,
                        'notes' => $attendance->notes,
                    ];
                })->values(),
                'statistics' => [
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                ],
            ];
        });

        // Generate week days
        $weekDays = [];
        $currentDay = $weekStart->copy();
        while ($currentDay <= $weekEnd) {
            $dayName = $currentDay->format('l'); // Monday, Tuesday, etc.
            $dayNumber = $currentDay->format('N'); // 1-7 (Monday-Sunday)
            
            // Check if this is an attendance day for any student
            $isAttendanceDay = false;
            foreach ($students as $student) {
                $attendanceDays = $student['attendance_days'] ?? [];
                $dayNames = [
                    1 => 'monday',
                    2 => 'tuesday',
                    3 => 'wednesday',
                    4 => 'thursday',
                    5 => 'friday',
                    6 => 'saturday',
                    7 => 'sunday',
                ];
                if (in_array($dayNames[$dayNumber], $attendanceDays)) {
                    $isAttendanceDay = true;
                    break;
                }
            }

            $weekDays[] = [
                'date' => $currentDay->format('Y-m-d'),
                'day_name' => $dayName,
                'day_number' => $dayNumber,
                'day_short' => $currentDay->format('D'),
                'day_formatted' => $currentDay->format('d M'),
                'is_today' => $currentDay->isToday(),
                'is_attendance_day' => $isAttendanceDay,
            ];
            
            $currentDay->addDay();
        }

        $groupData = [
            'id' => $group->id,
            'name' => $group->getName($locale),
            'course_name' => $group->course->getName($locale),
        ];

        return Inertia::render('Teacher/Attendance/Show', [
            'group' => $groupData,
            'students' => $students,
            'weekDays' => $weekDays,
            'selectedDate' => $selectedDate->format('Y-m-d'),
            'weekStart' => $weekStart->format('Y-m-d'),
            'weekEnd' => $weekEnd->format('Y-m-d'),
        ]);
    }

    /**
     * Store or update attendance.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id' => 'required|exists:groups,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,excused',
            'notes' => 'nullable|string|max:500',
        ]);

        // Ensure the group belongs to the teacher
        $group = Group::findOrFail($validated['group_id']);
        if ($group->teacher_id !== $user->id) {
            abort(403, 'You can only manage attendance for your own groups.');
        }

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'group_id' => $validated['group_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully.',
            'attendance' => [
                'id' => $attendance->id,
                'date' => $attendance->date->format('Y-m-d'),
                'status' => $attendance->status,
                'notes' => $attendance->notes,
            ],
        ]);
    }

    /**
     * Bulk update attendance for multiple students.
     */
    public function bulkStore(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,excused',
            'attendances.*.notes' => 'nullable|string|max:500',
        ]);

        // Ensure the group belongs to the teacher
        $group = Group::findOrFail($validated['group_id']);
        if ($group->teacher_id !== $user->id) {
            abort(403, 'You can only manage attendance for your own groups.');
        }

        DB::beginTransaction();
        try {
            foreach ($validated['attendances'] as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'group_id' => $validated['group_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'notes' => $attendanceData['notes'] ?? null,
                    ]
                );
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance saved successfully for all students.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save attendance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update student attendance days.
     */
    public function updateAttendanceDays(Request $request, Student $student)
    {
        $validated = $request->validate([
            'attendance_days' => 'required|array',
            'attendance_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $student->update([
            'attendance_days' => $validated['attendance_days'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance days updated successfully.',
            'attendance_days' => $student->attendance_days,
        ]);
    }

    /**
     * Bulk update attendance days for all students in a group.
     */
    public function bulkUpdateAttendanceDays(Request $request, Group $group)
    {
        $user = $request->user();
        
        // Ensure the group belongs to the teacher
        if ($group->teacher_id !== $user->id) {
            abort(403, 'You can only manage attendance for your own groups.');
        }

        $validated = $request->validate([
            'attendance_days' => 'required|array',
            'attendance_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $students = $group->students;
        
        foreach ($students as $student) {
            $student->update([
                'attendance_days' => $validated['attendance_days'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Attendance days updated successfully for {$students->count()} students.",
        ]);
    }
}
