<?php

namespace App\Http\Controllers\Sections;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PhotoController extends Controller
{
    public function index()
    {
        return Inertia::render('Sections/Photo');
    }
}

