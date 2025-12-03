<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::ordered()->with('branches')->get();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function create()
    {
        $branches = Branch::active()->ordered()->get();

        return Inertia::render('Admin/Courses/Create', [
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_hy' => 'required|string|max:255',
            'name_ge' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug',
            'price' => 'required|numeric|min:0',
            'months' => 'required|integer|min:1',
            'background_image' => 'nullable|image|max:2048',
            'description_en' => 'nullable|string',
            'description_hy' => 'nullable|string',
            'description_ge' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'branches' => 'nullable|array',
            'branches.*' => 'exists:branches,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')->store('courses', 'public');
        }

        $branches = $validated['branches'] ?? [];
        unset($validated['branches']);

        $course = Course::create($validated);
        $course->branches()->sync($branches);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $course->load('branches');
        $branches = Branch::active()->ordered()->get();

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'branches' => $branches,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_hy' => 'required|string|max:255',
            'name_ge' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug,' . $course->id,
            'price' => 'required|numeric|min:0',
            'months' => 'required|integer|min:1',
            'background_image' => 'nullable|image|max:2048',
            'description_en' => 'nullable|string',
            'description_hy' => 'nullable|string',
            'description_ge' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'branches' => 'nullable|array',
            'branches.*' => 'exists:branches,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_en']);
        }

        if ($request->hasFile('background_image')) {
            if ($course->background_image) {
                Storage::disk('public')->delete($course->background_image);
            }
            $validated['background_image'] = $request->file('background_image')->store('courses', 'public');
        }

        $branches = $validated['branches'] ?? [];
        unset($validated['branches']);

        $course->update($validated);
        $course->branches()->sync($branches);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->background_image) {
            Storage::disk('public')->delete($course->background_image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
