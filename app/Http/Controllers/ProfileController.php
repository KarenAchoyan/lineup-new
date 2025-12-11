<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Message;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Payment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $locale = app()->getLocale();

        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $students = $user->students()->with(['course', 'branch', 'payments', 'groups'])->get()->map(function ($student) use ($locale, $currentMonth, $currentYear) {
            // Get groups for this student
            $groups = $student->groups()->with('course')->get()->map(function ($group) use ($locale, $student, $currentMonth, $currentYear) {
                // Calculate attendance statistics for this month
                $presentCount = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'present')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->count();

                $absentCount = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'absent')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->count();

                $excusedCount = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'excused')
                    ->whereMonth('date', $currentMonth)
                    ->whereYear('date', $currentYear)
                    ->count();

                $totalCount = $presentCount + $absentCount + $excusedCount;
                $attendancePercentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;

                // Get recent attendance (last 7 days)
                $weekStart = Carbon::now()->startOfWeek();
                $recentAttendance = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('date', '>=', $weekStart)
                    ->orderBy('date', 'desc')
                    ->get()
                    ->map(function ($attendance) {
                        return [
                            'date' => $attendance->date->format('Y-m-d'),
                            'date_human' => $attendance->date->format('M d'),
                            'status' => $attendance->status,
                            'notes' => $attendance->notes,
                        ];
                    });

                // Check if student has paid for this group this month
                $hasPaidForGroup = Payment::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'success')
                    ->whereYear('paid_at', $currentYear)
                    ->whereMonth('paid_at', $currentMonth)
                    ->exists();

                return [
                    'id' => $group->id,
                    'name' => $group->getName($locale),
                    'course_name' => $group->course->getName($locale),
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                    'excused_count' => $excusedCount,
                    'total_count' => $totalCount,
                    'attendance_percentage' => $attendancePercentage,
                    'recent_attendance' => $recentAttendance,
                    'has_paid_this_month' => $hasPaidForGroup,
                ];
            });

            return [
                'id' => $student->id,
                'name' => $student->name,
                'biradi' => $student->biradi,
                'birthday' => $student->birthday?->format('Y-m-d'),
                'course_id' => $student->course_id,
                'course_name' => $student->course->getName($locale),
                'course_price' => $student->course->price,
                'branch_id' => $student->branch_id,
                'branch_name' => $student->branch->getName($locale),
                'has_paid_this_month' => $student->hasPaidThisMonth(),
                'groups' => $groups,
            ];
        });

        $courses = Course::active()
            ->ordered()
            ->with('branches')
            ->get()
            ->map(function ($course) use ($locale) {
                return [
                    'id' => $course->id,
                    'name' => $course->getName($locale),
                    'branches' => $course->branches->map(function ($branch) use ($locale) {
                        return [
                            'id' => $branch->id,
                            'name' => $branch->getName($locale),
                        ];
                    }),
                ];
            });

        // Get messages for the user (only current month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $messages = Message::where('to_user_id', $user->id)
            ->whereNull('parent_message_id') // Only root messages
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->with(['fromUser', 'replies' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) {
                $latestMessage = $message->replies->last() ?? $message;
                
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'from_user_name' => $message->fromUser->name,
                    'from_user_email' => $message->fromUser->email,
                    'student_ids' => $message->student_ids ?? [],
                    'has_students' => !empty($message->student_ids),
                    'is_read' => $message->isRead(),
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $message->created_at->diffForHumans(),
                    'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
                    'updated_at_human' => $message->updated_at->diffForHumans(),
                    'latest_message_snippet' => \Illuminate\Support\Str::limit($latestMessage->message, 100),
                ];
            });

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'students' => $students,
            'courses' => $courses,
            'messages' => $messages,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Display the user's settings page.
     */
    public function settings(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Profile/Settings', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Mark a message as read.
     */
    public function markMessageAsRead(Request $request, Message $message): RedirectResponse
    {
        // Ensure the message belongs to the authenticated user
        if ($message->to_user_id !== $request->user()->id) {
            abort(403);
        }

        $message->markAsRead();

        return redirect()->route('profile.edit');
    }

    /**
     * Store a new student sub-profile.
     */
    public function storeStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0531}-\x{0556}\x{0561}-\x{0587}\s]+$/u', // Only Armenian letters and spaces
            ],
            'biradi' => 'nullable|string|max:255|unique:students,biradi',
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'birthday' => 'required|date',
        ], [
            'name.regex' => 'Ուսանողի անունը պետք է պարունակի միայն հայատառ տառեր:',
        ]);

        // Verify that the branch belongs to the selected course
        $course = Course::findOrFail($validated['course_id']);
        if (!$course->branches->contains($validated['branch_id'])) {
            return Redirect::back()->withErrors(['branch_id' => 'The selected branch is not available for this course.']);
        }

        $request->user()->students()->create($validated);

        return Redirect::route('profile.edit')->with('status', 'Student profile created successfully.');
    }

    /**
     * Update a student sub-profile.
     */
    public function updateStudent(Request $request, Student $student): RedirectResponse
    {
        // Ensure the student belongs to the authenticated user
        if ($student->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0531}-\x{0556}\x{0561}-\x{0587}\s]+$/u', // Only Armenian letters and spaces
            ],
            'biradi' => 'nullable|string|max:255|unique:students,biradi,' . $student->id,
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'birthday' => 'nullable|date',
        ], [
            'name.regex' => 'Ուսանողի անունը պետք է պարունակի միայն հայատառ տառեր:',
        ]);

        // Verify that the branch belongs to the selected course
        $course = Course::findOrFail($validated['course_id']);
        if (!$course->branches->contains($validated['branch_id'])) {
            return Redirect::back()->withErrors(['branch_id' => 'The selected branch is not available for this course.']);
        }

        $student->update($validated);

        return Redirect::route('profile.edit')->with('status', 'Student profile updated successfully.');
    }

    /**
     * Delete a student sub-profile.
     */
    public function destroyStudent(Request $request, Student $student): RedirectResponse
    {
        // Ensure the student belongs to the authenticated user
        if ($student->user_id !== $request->user()->id) {
            abort(403);
        }

        $student->delete();

        return Redirect::route('profile.edit')->with('status', 'Student profile deleted successfully.');
    }

    /**
     * Get attendance data for a specific month.
     */
    public function getAttendanceData(Request $request)
    {
        $user = $request->user();
        $locale = app()->getLocale();
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $students = $user->students()->with(['course', 'branch', 'groups'])->get()->map(function ($student) use ($locale, $month, $year) {
            // Get groups for this student
            $groups = $student->groups()->with('course')->get()->map(function ($group) use ($locale, $student, $month, $year) {
                // Calculate attendance statistics for selected month
                $presentCount = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'present')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->count();

                $absentCount = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'absent')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->count();

                $excusedCount = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->where('status', 'excused')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->count();

                $totalCount = $presentCount + $absentCount + $excusedCount;
                $attendancePercentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;

                // Get all attendance for the month
                $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
                $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();
                
                $monthAttendance = Attendance::where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->whereBetween('date', [$monthStart, $monthEnd])
                    ->orderBy('date', 'desc')
                    ->get()
                    ->map(function ($attendance) {
                        return [
                            'date' => $attendance->date->format('Y-m-d'),
                            'date_human' => $attendance->date->format('M d'),
                            'status' => $attendance->status,
                            'notes' => $attendance->notes,
                        ];
                    });

                return [
                    'id' => $group->id,
                    'name' => $group->getName($locale),
                    'course_name' => $group->course->getName($locale),
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                    'excused_count' => $excusedCount,
                    'total_count' => $totalCount,
                    'attendance_percentage' => $attendancePercentage,
                    'recent_attendance' => $monthAttendance,
                ];
            });

            return [
                'id' => $student->id,
                'name' => $student->name,
                'course_name' => $student->course->getName($locale),
                'branch_name' => $student->branch->getName($locale),
                'groups' => $groups,
            ];
        });

        return response()->json([
            'students' => $students,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
