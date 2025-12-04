<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AboutController extends Controller
{
    /**
     * Show the form for editing the about page.
     *
     * @return \Inertia\Response
     */
    public function edit()
    {
        $about = About::getAbout();

        return Inertia::render('Admin/About/Edit', [
            'about' => $about,
        ]);
    }

    /**
     * Update the about page content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('About update request received', [
            'all_data' => $request->all(),
            'has_image' => $request->hasFile('image'),
            'about_en_length' => strlen($request->input('about_en', '')),
            'about_hy_length' => strlen($request->input('about_hy', '')),
        ]);

        // Validate the request
        $validated = $request->validate([
            'about_en' => 'nullable|string',
            'about_hy' => 'nullable|string',
            'about_ge' => 'nullable|string',
            'about_ru' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Get or create the about record
            $about = About::getAbout();

            // Get all text fields from request - FormData sends all fields
            $updateData = [
                'about_en' => $request->input('about_en', ''),
                'about_hy' => $request->input('about_hy', ''),
                'about_ge' => $request->input('about_ge', ''),
                'about_ru' => $request->input('about_ru', ''),
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($about->image) {
                    $imagePath = str_replace('public/', '', $about->image);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
                
                // Store new image
                $updateData['image'] = 'public/' . $request->file('image')->store('about', 'public');
            }
            // If no image is uploaded, keep the existing image (don't update the field)

            // Log what we're about to update
            Log::info('About update data prepared', [
                'update_data' => $updateData,
                'about_id' => $about->id,
            ]);

            // Update the model - this will update all specified fields
            $about->update($updateData);

            // Refresh the model to get updated data
            $about->refresh();

            // Log successful update
            Log::info('About page updated successfully', [
                'about_id' => $about->id,
                'updated_fields' => array_keys($updateData),
                'new_values' => $updateData,
            ]);

            return redirect()->route('admin.about.edit')
                ->with('success', 'About page updated successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating about page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return redirect()->route('admin.about.edit')
                ->with('error', 'An error occurred while updating the about page: ' . $e->getMessage());
        }
    }
}
