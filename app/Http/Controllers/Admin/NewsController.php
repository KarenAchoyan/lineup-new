<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::ordered()->get();

        return Inertia::render('Admin/News/Index', [
            'news' => $news,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/News/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_hy' => 'nullable|string|max:255',
            'title_ge' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'content_en' => 'nullable|string',
            'content_hy' => 'nullable|string',
            'content_ge' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'video' => 'nullable|url',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title_en']);
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('news', 'public');
        }

        News::create($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'News created successfully.');
    }

    public function edit(News $news)
    {
        return Inertia::render('Admin/News/Edit', [
            'news' => $news,
        ]);
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_hy' => 'nullable|string|max:255',
            'title_ge' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'content_en' => 'nullable|string',
            'content_hy' => 'nullable|string',
            'content_ge' => 'nullable|string',
            'content_ru' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'video' => 'nullable|url',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $news->id,
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title_en']);
        }

        // Only update avatar if a new file is uploaded
        if ($request->hasFile('avatar')) {
            if ($news->avatar) {
                Storage::disk('public')->delete($news->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('news', 'public');
        } else {
            // Remove avatar from validated array to preserve existing avatar
            unset($validated['avatar']);
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        if ($news->avatar) {
            Storage::disk('public')->delete($news->avatar);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News deleted successfully.');
    }
}
