<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::ordered()->get();

        return Inertia::render('Admin/Staff/Index', [
            'staffs' => $staffs,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname_en' => 'required|string|max:255',
            'fullname_hy' => 'required|string|max:255',
            'fullname_ge' => 'required|string|max:255',
            'fullname_ru' => 'required|string|max:255',
            'profession_en' => 'nullable|string|max:255',
            'profession_hy' => 'nullable|string|max:255',
            'profession_ge' => 'nullable|string|max:255',
            'profession_ru' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = 'public/' . $request->file('avatar')->store('staff', 'public');
        }

        Staff::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(Staff $staff)
    {
        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'fullname_en' => 'required|string|max:255',
            'fullname_hy' => 'required|string|max:255',
            'fullname_ge' => 'required|string|max:255',
            'fullname_ru' => 'required|string|max:255',
            'profession_en' => 'nullable|string|max:255',
            'profession_hy' => 'nullable|string|max:255',
            'profession_ge' => 'nullable|string|max:255',
            'profession_ru' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Handle image upload - only update avatar if a new file is uploaded
        if ($request->hasFile('avatar')) {
            // Delete old image if exists
            if ($staff->avatar) {
                $imagePath = str_replace('public/', '', $staff->avatar);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            // Store new image
            $validated['avatar'] = 'public/' . $request->file('avatar')->store('staff', 'public');
        } else {
            // Remove avatar from validated array if no new file is uploaded
            // This ensures the existing avatar is not overwritten
            unset($validated['avatar']);
        }

        $staff->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        if ($staff->avatar) {
            $imagePath = str_replace('public/', '', $staff->avatar);
            Storage::disk('public')->delete($imagePath);
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}
