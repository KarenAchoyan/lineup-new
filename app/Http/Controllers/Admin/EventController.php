<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::ordered()->get();

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Events/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
            'content_en' => 'nullable|string',
            'content_hy' => 'nullable|string',
            'content_ge' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug('event-' . $validated['event_date'] . '-' . time());
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = 'app/public/' . $request->file('avatar')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return Inertia::render('Admin/Events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
            'content_en' => 'nullable|string',
            'content_hy' => 'nullable|string',
            'content_ge' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'slug' => 'nullable|string|max:255|unique:events,slug,' . $event->id,
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug('event-' . $validated['event_date'] . '-' . time());
        }

        // Only update avatar if a new file is uploaded
        if ($request->hasFile('avatar')) {
            if ($event->avatar) {
                $imagePath = str_replace('app/public/', '', $event->avatar);
                Storage::disk('public')->delete($imagePath);
            }
            $validated['avatar'] = 'app/public/' . $request->file('avatar')->store('events', 'public');
        } else {
            // Remove avatar from validated array to preserve existing avatar
            unset($validated['avatar']);
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        if ($event->avatar) {
            $imagePath = str_replace('app/public/', '', $event->avatar);
            Storage::disk('public')->delete($imagePath);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
