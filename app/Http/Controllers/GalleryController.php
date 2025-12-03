<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Inertia\Inertia;

class GalleryController extends Controller
{
    /**
     * Display all galleries
     */
    public function index()
    {
        $galleries = Gallery::active()
            ->ordered()
            ->get()
            ->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'image' => $gallery->image,
                ];
            });

        return Inertia::render('Gallery', [
            'galleries' => $galleries,
        ]);
    }
}

