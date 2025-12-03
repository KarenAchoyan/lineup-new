<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Staff;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        $about = About::getAbout();
        $staffs = Staff::active()
            ->ordered()
            ->get()
            ->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'fullname' => $staff->getFullname(app()->getLocale()),
                    'fullname_en' => $staff->fullname_en,
                    'fullname_hy' => $staff->fullname_hy,
                    'fullname_ge' => $staff->fullname_ge,
                    'fullname_ru' => $staff->fullname_ru,
                    'profession' => $staff->getProfession(app()->getLocale()),
                    'profession_en' => $staff->profession_en,
                    'profession_hy' => $staff->profession_hy,
                    'profession_ge' => $staff->profession_ge,
                    'profession_ru' => $staff->profession_ru,
                    'phone' => $staff->phone,
                    'email' => $staff->email,
                    'avatar' => $staff->avatar,
                ];
            });

        return Inertia::render('About', [
            'about' => [
                'content' => $about->getContent(app()->getLocale()),
                'content_en' => $about->about_en,
                'content_hy' => $about->about_hy,
                'content_ge' => $about->about_ge,
                'content_ru' => $about->about_ru,
                'image' => $about->image,
            ],
            'staffs' => $staffs,
        ]);
    }
}
