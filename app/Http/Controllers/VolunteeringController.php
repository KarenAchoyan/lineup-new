<?php

namespace App\Http\Controllers;

use App\Models\VolunteeringSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VolunteeringController extends Controller
{
    public function index()
    {
        return Inertia::render('Volunteering');
    }

    public function registration()
    {
        return Inertia::render('Volunteering/Registration');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        VolunteeringSubmission::create($validated);

        return redirect()->route('volunteering.registration')
            ->with('success', true);
    }
}
