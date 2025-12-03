<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * Show the form for editing the banner.
     *
     * @return \Inertia\Response
     */
    public function edit()
    {
        $banner = Banner::getBanner();

        return Inertia::render('Admin/Banner/Edit', [
            'banner' => $banner,
        ]);
    }

    /**
     * Update the banner content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('Banner update request received', [
            'has_image' => $request->hasFile('image'),
        ]);

        // Validate the request
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'about_dancing_en' => 'nullable|string|max:255',
            'dancing_en' => 'nullable|string|max:255',
            'about_dancing_hy' => 'nullable|string|max:255',
            'dancing_hy' => 'nullable|string|max:255',
            'about_dancing_ge' => 'nullable|string|max:255',
            'dancing_ge' => 'nullable|string|max:255',
            'about_dancing_ru' => 'nullable|string|max:255',
            'dancing_ru' => 'nullable|string|max:255',
        ]);

        try {
            // Get or create the banner record
            $banner = Banner::getBanner();

            // Get all text fields from request
            $updateData = [
                'about_dancing_en' => $request->input('about_dancing_en') ?? '',
                'dancing_en' => $request->input('dancing_en') ?? '',
                'about_dancing_hy' => $request->input('about_dancing_hy') ?? '',
                'dancing_hy' => $request->input('dancing_hy') ?? '',
                'about_dancing_ge' => $request->input('about_dancing_ge') ?? '',
                'dancing_ge' => $request->input('dancing_ge') ?? '',
                'about_dancing_ru' => $request->input('about_dancing_ru') ?? '',
                'dancing_ru' => $request->input('dancing_ru') ?? '',
            ];

            // Log the data being saved
            Log::info('Banner update data prepared', [
                'update_data' => $updateData,
                'banner_id' => $banner->id,
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
                
                // Store new image
                $updateData['image'] = $request->file('image')->store('banners', 'public');
            }

            // Update the model
            $banner->update($updateData);

            // Refresh the model to get updated data
            $banner->refresh();

            // Log successful update with actual saved values
            Log::info('Banner updated successfully', [
                'banner_id' => $banner->id,
                'updated_fields' => array_keys($updateData),
                'saved_values' => [
                    'about_dancing_en' => $banner->about_dancing_en,
                    'dancing_en' => $banner->dancing_en,
                    'about_dancing_hy' => $banner->about_dancing_hy,
                    'dancing_hy' => $banner->dancing_hy,
                    'about_dancing_ge' => $banner->about_dancing_ge,
                    'dancing_ge' => $banner->dancing_ge,
                    'about_dancing_ru' => $banner->about_dancing_ru,
                    'dancing_ru' => $banner->dancing_ru,
                ],
            ]);

            return redirect()->route('admin.banner.edit')
                ->with('success', 'Banner updated successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating banner', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return redirect()->route('admin.banner.edit')
                ->with('error', 'An error occurred while updating the banner: ' . $e->getMessage());
        }
    }
}






