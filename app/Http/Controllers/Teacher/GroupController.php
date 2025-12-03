<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the teacher's groups.
     */
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $user = $request->user();
        $search = $request->input('search', '');
        $courseId = $request->input('course_id');

        $query = Group::where('teacher_id', $user->id)->with(['course', 'students.user'])->ordered();

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
            ];
        });

        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        return Inertia::render('Teacher/Groups/Index', [
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

        return Inertia::render('Teacher/Groups/Create', [
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

        // Set teacher_id to current user
        $validated['teacher_id'] = $request->user()->id;

        $group = Group::create($validated);
        $group->students()->sync($students);

        return redirect()->route('teacher.groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Group $group)
    {
        // Ensure the group belongs to the teacher
        if ($group->teacher_id !== $request->user()->id) {
            abort(403, 'You can only edit your own groups.');
        }

        $locale = app()->getLocale();
        $group->load(['course', 'students.user']);

        $courses = Course::active()->ordered()->get()->map(function ($course) use ($locale) {
            return [
                'id' => $course->id,
                'name' => $course->getName($locale),
            ];
        });

        return Inertia::render('Teacher/Groups/Edit', [
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
        // Ensure the group belongs to the teacher
        if ($group->teacher_id !== $request->user()->id) {
            abort(403, 'You can only update your own groups.');
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
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
        ]);

        $students = $validated['students'] ?? [];
        unset($validated['students']);

        $group->update($validated);
        $group->students()->sync($students);

        return redirect()->route('teacher.groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Group $group)
    {
        // Ensure the group belongs to the teacher
        if ($group->teacher_id !== $request->user()->id) {
            abort(403, 'You can only delete your own groups.');
        }

        $group->delete();

        return redirect()->route('teacher.groups.index')
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
}
