<?php

namespace App\Http\Controllers;

use App\Models\Privacy;
use Inertia\Inertia;

class PrivacyController extends Controller
{
    public function index()
    {
        $privacy = Privacy::getPrivacy();
        $locale = app()->getLocale();
        
        return Inertia::render('Privacy', [
            'content' => $privacy->getContent($locale),
        ]);
    }
}

