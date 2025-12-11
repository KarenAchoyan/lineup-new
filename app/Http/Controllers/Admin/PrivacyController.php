<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Privacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PrivacyController extends Controller
{
    /**
     * Show the form for editing the privacy policy page.
     *
     * @return \Inertia\Response
     */
    public function edit()
    {
        $privacy = Privacy::getPrivacy();

        return Inertia::render('Admin/Privacy/Edit', [
            'privacy' => $privacy,
        ]);
    }

    /**
     * Update the privacy policy page content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('Privacy update request received', [
            'all_data' => $request->all(),
            'content_en_length' => strlen($request->input('content_en', '')),
            'content_hy_length' => strlen($request->input('content_hy', '')),
        ]);

        // Validate the request
        $validated = $request->validate([
            'content_en' => 'nullable|string',
            'content_hy' => 'nullable|string',
            'content_ge' => 'nullable|string',
            'content_ru' => 'nullable|string',
        ]);

        try {
            // Get or create the privacy record
            $privacy = Privacy::getPrivacy();

            // Get all text fields from request
            $updateData = [
                'content_en' => $request->input('content_en', ''),
                'content_hy' => $request->input('content_hy', ''),
                'content_ge' => $request->input('content_ge', ''),
                'content_ru' => $request->input('content_ru', ''),
            ];

            // Log what we're about to update
            Log::info('Privacy update data prepared', [
                'update_data' => $updateData,
                'privacy_id' => $privacy->id,
            ]);

            // Update the model
            $privacy->update($updateData);

            // Refresh the model to get updated data
            $privacy->refresh();

            // Log successful update
            Log::info('Privacy page updated successfully', [
                'privacy_id' => $privacy->id,
                'updated_fields' => array_keys($updateData),
            ]);

            return redirect()->route('admin.privacy.edit')
                ->with('success', 'Privacy policy updated successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating privacy page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return redirect()->route('admin.privacy.edit')
                ->with('error', 'An error occurred while updating the privacy policy: ' . $e->getMessage());
        }
    }
}
