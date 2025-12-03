<?php

namespace App\Http\Controllers\Sections;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Sections/Dance');
    }
}

