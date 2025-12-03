<?php

namespace App\Http\Controllers;

use App\Models\ActiveUser;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\News;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Get banner data
        $banner = Banner::getBanner();

        // Get latest news
        $news = News::active()
            ->latest()
            ->first();

        // Get active users
        $activeUsers = ActiveUser::active()
            ->ordered()
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'fullname_en' => $user->fullname_en,
                    'fullname_hy' => $user->fullname_hy,
                    'fullname_ge' => $user->fullname_ge,
                    'fullname_ru' => $user->fullname_ru,
                    'avatar' => $user->avatar,
                ];
            });

        // Get galleries (achievements)
        $galleries = Gallery::active()
            ->ordered()
            ->get()
            ->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'image' => $gallery->image,
                ];
            });

        // Get events
        $events = Event::active()
            ->upcoming()
            ->ordered()
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'content_en' => $event->content_en,
                    'content_hy' => $event->content_hy,
                    'content_ge' => $event->content_ge,
                    'content_ru' => $event->content_ru,
                    'avatar' => $event->avatar,
                    'slug' => $event->slug,
                ];
            });

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'banner' => $banner ? [
                'id' => $banner->id,
                'image' => $banner->image,
                'about_dancing_en' => $banner->about_dancing_en ?? '',
                'dancing_en' => $banner->dancing_en ?? '',
                'about_dancing_hy' => $banner->about_dancing_hy ?? '',
                'dancing_hy' => $banner->dancing_hy ?? '',
                'about_dancing_ge' => $banner->about_dancing_ge ?? '',
                'dancing_ge' => $banner->dancing_ge ?? '',
                'about_dancing_ru' => $banner->about_dancing_ru ?? '',
                'dancing_ru' => $banner->dancing_ru ?? '',
            ] : null,
            'news' => $news ? [
                'id' => $news->id,
                'title_en' => $news->title_en,
                'title_hy' => $news->title_hy,
                'title_ge' => $news->title_ge,
                'title_ru' => $news->title_ru,
                'content_en' => $news->content_en,
                'content_hy' => $news->content_hy,
                'content_ge' => $news->content_ge,
                'content_ru' => $news->content_ru,
                'avatar' => $news->avatar,
                'video' => $news->video,
                'slug' => $news->slug,
            ] : null,
            'activeUsers' => $activeUsers,
            'galleries' => $galleries,
            'events' => $events,
        ]);
    }
}
