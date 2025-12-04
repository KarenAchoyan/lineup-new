<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActiveUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ActiveUserController extends Controller
{
    public function index()
    {
        $activeUsers = ActiveUser::ordered()->get();

        return Inertia::render('Admin/ActiveUsers/Index', [
            'activeUsers' => $activeUsers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ActiveUsers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname_en' => 'required|string|max:255',
            'fullname_hy' => 'nullable|string|max:255',
            'fullname_ge' => 'nullable|string|max:255',
            'fullname_ru' => 'nullable|string|max:255',
            'about_en' => 'nullable|string',
            'about_hy' => 'nullable|string',
            'about_ge' => 'nullable|string',
            'about_ru' => 'nullable|string',
            'avatar' => 'required|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = 'public/' . $request->file('avatar')->store('active-users', 'public');
        }

        ActiveUser::create($validated);

        return redirect()->route('admin.active-users.index')
            ->with('success', 'Active user created successfully.');
    }

    public function edit(ActiveUser $activeUser)
    {
        return Inertia::render('Admin/ActiveUsers/Edit', [
            'activeUser' => $activeUser,
        ]);
    }

    public function update(Request $request, ActiveUser $activeUser)
    {
        $validated = $request->validate([
            'fullname_en' => 'required|string|max:255',
            'fullname_hy' => 'nullable|string|max:255',
            'fullname_ge' => 'nullable|string|max:255',
            'fullname_ru' => 'nullable|string|max:255',
            'about_en' => 'nullable|string',
            'about_hy' => 'nullable|string',
            'about_ge' => 'nullable|string',
            'about_ru' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            if ($activeUser->avatar) {
                $imagePath = str_replace('public/', '', $activeUser->avatar);
                Storage::disk('public')->delete($imagePath);
            }
            $validated['avatar'] = 'public/' . $request->file('avatar')->store('active-users', 'public');
        } else {
            // Remove avatar from validated data if no new file is uploaded
            unset($validated['avatar']);
        }

        $activeUser->update($validated);

        return redirect()->route('admin.active-users.index')
            ->with('success', 'Active user updated successfully.');
    }

    public function destroy(ActiveUser $activeUser)
    {
        if ($activeUser->avatar) {
            $imagePath = str_replace('public/', '', $activeUser->avatar);
            Storage::disk('public')->delete($imagePath);
        }

        $activeUser->delete();

        return redirect()->route('admin.active-users.index')
            ->with('success', 'Active user deleted successfully.');
    }
}
