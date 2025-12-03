<?php

namespace App\Http\Controllers;

use App\Models\ArchiveItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = ArchiveItem::active()->ordered();

        // Filter by year if provided
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $items = $query->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'year' => $item->year,
                    'image' => $item->image,
                    'youtube_link' => $item->youtube_link,
                    'youtube_id' => $item->youtube_id,
                    'youtube_embed_url' => $item->youtube_embed_url,
                    'link' => $item->link,
                    'title' => $item->getTitle(app()->getLocale()),
                    'title_en' => $item->title_en,
                    'title_hy' => $item->title_hy,
                    'title_ge' => $item->title_ge,
                    'title_ru' => $item->title_ru,
                    'description' => $item->getDescription(app()->getLocale()),
                    'description_en' => $item->description_en,
                    'description_hy' => $item->description_hy,
                    'description_ge' => $item->description_ge,
                    'description_ru' => $item->description_ru,
                ];
            });

        // Get all available years
        $years = ArchiveItem::active()
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $images = $items->where('type', 'image')->values();
        $videos = $items->where('type', 'video')->values();
        $news = $items->where('type', 'news')->values();

        return Inertia::render('Archive', [
            'images' => $images,
            'videos' => $videos,
            'news' => $news,
            'years' => $years,
            'selectedYear' => $request->year,
        ]);
    }
}
