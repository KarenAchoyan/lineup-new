<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display all users with their student sub-profiles.
     */
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        // Get filter parameters
        $search = $request->input('search', '');
        $courseId = $request->input('course_id');
        $branchId = $request->input('branch_id');
        $hasStudents = $request->input('has_students');
        $studentType = $request->input('student_type', ''); // 'new' or 'old'
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Build query
        $query = User::with(['students.course', 'students.branch', 'students.groups', 'roles'])
            ->whereDoesntHave('roles', function ($query) {
                $query->where('slug', 'admin');
            });

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by course
        if (!empty($courseId)) {
            $query->whereHas('students', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        // Filter by branch
        if (!empty($branchId)) {
            $query->whereHas('students', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        // Filter by students count
        if (!empty($hasStudents)) {
            if ($hasStudents === 'yes') {
                $query->has('students');
            } elseif ($hasStudents === 'no') {
                $query->doesntHave('students');
            }
        }

        // Filter by registration date
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Apply sorting
        $validSortColumns = ['name', 'email', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'created_at';
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->get()->map(function ($user) use ($locale, $courseId, $branchId, $studentType) {
            // Filter students based on course and branch filters
            $filteredStudents = $user->students;
            
            if (!empty($courseId)) {
                $filteredStudents = $filteredStudents->filter(function ($student) use ($courseId) {
                    return $student->course_id == $courseId;
                });
            }
            
            if (!empty($branchId)) {
                $filteredStudents = $filteredStudents->filter(function ($student) use ($branchId) {
                    return $student->branch_id == $branchId;
                });
            }

            // Filter by student type (new = no groups, old = has groups)
            if (!empty($studentType)) {
                $filteredStudents = $filteredStudents->filter(function ($student) use ($studentType) {
                    $hasGroups = $student->groups->count() > 0;
                    if ($studentType === 'new') {
                        return !$hasGroups; // New students = no groups
                    } elseif ($studentType === 'old') {
                        return $hasGroups; // Old students = has groups
                    }
                    return true;
                });
            }
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'is_teacher' => $user->isTeacher(),
                'students' => $filteredStudents->map(function ($student) use ($locale) {
                    $hasGroups = $student->groups->count() > 0;
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'birthday' => $student->birthday?->format('Y-m-d'),
                        'course_id' => $student->course_id,
                        'branch_id' => $student->branch_id,
                        'course_name' => $student->course->getName($locale),
                        'branch_name' => $student->branch->getName($locale),
                        'has_groups' => $hasGroups,
                        'groups_count' => $student->groups->count(),
                    ];
                })->values(), // Use values() to reindex the collection
            ];
        })->filter(function ($user) use ($studentType) {
            // If student_type filter is set, only show users with matching students
            if (!empty($studentType) && $user['students']->count() === 0) {
                return false;
            }
            return true;
        })->values();

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

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'courses' => $courses,
            'branches' => $branches,
            'filters' => [
                'search' => $search,
                'course_id' => $courseId,
                'branch_id' => $branchId,
                'has_students' => $hasStudents,
                'student_type' => $studentType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Get student groups for management modal
     */
    public function getStudentGroups(Student $student)
    {
        $locale = app()->getLocale();
        
        $student->load('groups.course');
        
        $allGroups = Group::active()->ordered()->with('course')->get()->map(function ($group) use ($locale, $student) {
            return [
                'id' => $group->id,
                'name' => $group->getName($locale),
                'course_name' => $group->course->getName($locale),
                'is_selected' => $student->groups->contains($group->id),
            ];
        });

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
            ],
            'groups' => $allGroups,
        ]);
    }

    /**
     * Update student groups
     */
    public function updateStudentGroups(Request $request, Student $student)
    {
        $validated = $request->validate([
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
        ]);

        $groupIds = $validated['groups'] ?? [];
        $student->groups()->sync($groupIds);

        return response()->json([
            'success' => true,
            'message' => 'Student groups updated successfully.',
        ]);
    }

    /**
     * Assign teacher role to a user.
     */
    public function assignTeacherRole(Request $request, User $user)
    {
        // Check if user has students
        if ($user->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign teacher role to a user who has students. Please remove all students first.',
            ], 422);
        }

        // Check if user is already a teacher
        if ($user->isTeacher()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a teacher.',
            ], 422);
        }

        // Get teacher role
        $teacherRole = Role::where('slug', 'teacher')->first();
        
        if (!$teacherRole) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher role not found.',
            ], 500);
        }

        // Assign teacher role
        $user->roles()->syncWithoutDetaching([$teacherRole->id]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher role assigned successfully.',
        ]);
    }

    /**
     * Remove teacher role from a user.
     */
    public function removeTeacherRole(Request $request, User $user)
    {
        // Check if user is a teacher
        if (!$user->isTeacher()) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a teacher.',
            ], 422);
        }

        // Get teacher role
        $teacherRole = Role::where('slug', 'teacher')->first();
        
        if (!$teacherRole) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher role not found.',
            ], 500);
        }

        // Remove teacher role
        $user->roles()->detach($teacherRole->id);

        return response()->json([
            'success' => true,
            'message' => 'Teacher role removed successfully.',
        ]);
    }
}
