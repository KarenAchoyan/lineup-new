<?php

namespace App\Http\Controllers;

use App\Models\ActiveUser;
use Inertia\Inertia;

class ActiveUserController extends Controller
{
    /**
     * Display a specific active user
     */
    public function show($id)
    {
        $activeUser = ActiveUser::where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $locale = app()->getLocale();

        return Inertia::render('ActiveUser/Show', [
            'activeUser' => [
                'id' => $activeUser->id,
                'fullname' => $activeUser->getFullname($locale),
                'fullname_en' => $activeUser->fullname_en,
                'fullname_hy' => $activeUser->fullname_hy,
                'fullname_ge' => $activeUser->fullname_ge,
                'fullname_ru' => $activeUser->fullname_ru,
                'about' => $activeUser->getAbout($locale),
                'about_en' => $activeUser->about_en,
                'about_hy' => $activeUser->about_hy,
                'about_ge' => $activeUser->about_ge,
                'about_ru' => $activeUser->about_ru,
                'avatar' => $activeUser->avatar,
            ],
        ]);
    }
}

