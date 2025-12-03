<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectionsController extends Controller
{
    /**
     * Display all sections/courses
     */
    public function index()
    {
        $courses = Course::active()
            ->ordered()
            ->with('branches')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->getName(app()->getLocale()),
                    'name_en' => $course->name_en,
                    'name_hy' => $course->name_hy,
                    'name_ge' => $course->name_ge,
                    'name_ru' => $course->name_ru,
                    'slug' => $course->slug,
                    'price' => $course->price,
                    'months' => $course->months,
                    'background_image' => $course->background_image,
                    'description' => $course->getDescription(app()->getLocale()),
                    'branches' => $course->branches->map(function ($branch) {
                        return [
                            'id' => $branch->id,
                            'name' => $branch->getName(app()->getLocale()),
                            'name_en' => $branch->name_en,
                            'name_hy' => $branch->name_hy,
                            'name_ge' => $branch->name_ge,
                            'name_ru' => $branch->name_ru,
                        ];
                    }),
                ];
            });

        return Inertia::render('Sections/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Display a specific course/section
     */
    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->active()
            ->with('branches')
            ->firstOrFail();

        $branches = $course->branches->map(function ($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->getName(app()->getLocale()),
                'name_en' => $branch->name_en,
                'name_hy' => $branch->name_hy,
                'name_ge' => $branch->name_ge,
                'name_ru' => $branch->name_ru,
            ];
        });

        return Inertia::render('Sections/Show', [
            'course' => [
                'id' => $course->id,
                'name' => $course->getName(app()->getLocale()),
                'name_en' => $course->name_en,
                'name_hy' => $course->name_hy,
                'name_ge' => $course->name_ge,
                'name_ru' => $course->name_ru,
                'slug' => $course->slug,
                'price' => $course->price,
                'months' => $course->months,
                'background_image' => $course->background_image,
                'description' => $course->getDescription(app()->getLocale()),
            ],
            'branches' => $branches,
        ]);
    }
}
