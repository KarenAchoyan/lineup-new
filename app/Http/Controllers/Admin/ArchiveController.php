<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = ArchiveItem::ordered()->get();

        return Inertia::render('Admin/Archive/Index', [
            'items' => $items,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Archive/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:image,video,news',
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'image' => 'nullable|image|max:2048',
            'youtube_link' => 'nullable|url',
            'link' => 'nullable|url',
            'title_en' => 'nullable|string|max:255',
            'title_hy' => 'nullable|string|max:255',
            'title_ge' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_hy' => 'nullable|string',
            'description_ge' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = 'app/public/' . $request->file('image')->store('archive', 'public');
        }

        ArchiveItem::create($validated);

        return redirect()->route('admin.archive.index')
            ->with('success', 'Archive item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ArchiveItem $archive)
    {
        return Inertia::render('Admin/Archive/Edit', [
            'item' => $archive,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArchiveItem $archive)
    {
        $validated = $request->validate([
            'type' => 'required|in:image,video,news',
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'image' => 'nullable|image|max:2048',
            'youtube_link' => 'nullable|url',
            'link' => 'nullable|url',
            'title_en' => 'nullable|string|max:255',
            'title_hy' => 'nullable|string|max:255',
            'title_ge' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_hy' => 'nullable|string',
            'description_ge' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Handle image upload - only update image if a new file is uploaded
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($archive->image) {
                $imagePath = str_replace('app/public/', '', $archive->image);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            // Store new image
            $validated['image'] = 'app/public/' . $request->file('image')->store('archive', 'public');
        } else {
            // Remove image from validated array if no new file is uploaded
            // This ensures the existing image is not overwritten
            unset($validated['image']);
        }

        $archive->update($validated);

        return redirect()->route('admin.archive.index')
            ->with('success', 'Archive item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArchiveItem $archive)
    {
        if ($archive->image) {
            $imagePath = str_replace('app/public/', '', $archive->image);
            Storage::disk('public')->delete($imagePath);
        }

        $archive->delete();

        return redirect()->route('admin.archive.index')
            ->with('success', 'Archive item deleted successfully.');
    }
}
