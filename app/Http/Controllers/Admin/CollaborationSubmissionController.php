<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollaborationSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollaborationSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $submissions = CollaborationSubmission::latest()->paginate(15);

        return Inertia::render('Admin/CollaborationSubmissions/Index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CollaborationSubmission $collaborationSubmission)
    {
        // Mark as read
        $collaborationSubmission->update(['is_read' => true]);

        return Inertia::render('Admin/CollaborationSubmissions/Show', [
            'submission' => $collaborationSubmission,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CollaborationSubmission $collaborationSubmission)
    {
        $collaborationSubmission->delete();

        return redirect()->route('admin.collaboration-submissions.index')
            ->with('success', 'Submission deleted successfully.');
    }

    /**
     * Mark submission as read/unread
     */
    public function toggleRead(CollaborationSubmission $collaborationSubmission)
    {
        $collaborationSubmission->update([
            'is_read' => !$collaborationSubmission->is_read,
        ]);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }
}
