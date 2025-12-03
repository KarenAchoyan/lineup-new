<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteeringSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VolunteeringSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $submissions = VolunteeringSubmission::latest()->paginate(15);

        return Inertia::render('Admin/VolunteeringSubmissions/Index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(VolunteeringSubmission $volunteeringSubmission)
    {
        // Mark as read
        $volunteeringSubmission->update(['is_read' => true]);

        return Inertia::render('Admin/VolunteeringSubmissions/Show', [
            'submission' => $volunteeringSubmission,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VolunteeringSubmission $volunteeringSubmission)
    {
        $volunteeringSubmission->delete();

        return redirect()->route('admin.volunteering-submissions.index')
            ->with('success', 'Submission deleted successfully.');
    }

    /**
     * Mark submission as read/unread
     */
    public function toggleRead(VolunteeringSubmission $volunteeringSubmission)
    {
        $volunteeringSubmission->update([
            'is_read' => !$volunteeringSubmission->is_read,
        ]);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }
}
