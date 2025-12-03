<?php

namespace App\Http\Controllers;

use App\Models\CollaborationSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollaborationController extends Controller
{
    public function index()
    {
        return Inertia::render('Collaboration');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        CollaborationSubmission::create($validated);

        return back()->with('success', true);
    }
}
