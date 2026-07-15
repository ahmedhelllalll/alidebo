<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function checkSlug(Request $request)
    {
        $slug = $request->slug;
        $ignoreId = $request->ignore;

        $exists = Page::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })->exists();

        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string',
            'slug' => 'nullable|string|unique:pages,slug',
            'content' => 'nullable|array',
            'status' => 'required|in:published,draft',
            'location' => 'nullable|in:hidden,navbar,footer,both',
            'layout_style' => 'nullable|in:default,cards,split',
            'seo_metadata' => 'nullable|array',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']['en']);
        $page = Page::create($data);

        $this->handleSeoMetadata($page, $request);

        return redirect()->route('admin.pages.index')->with('success', __('admin.page_created'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string',
            'slug' => 'nullable|string|unique:pages,slug,' . $page->id,
            'content' => 'nullable|array',
            'status' => 'required|in:published,draft',
            'location' => 'nullable|in:hidden,navbar,footer,both',
            'layout_style' => 'nullable|in:default,cards,split',
            'seo_metadata' => 'nullable|array',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']['en']);
        $page->update($data);

        $this->handleSeoMetadata($page, $request);

        return redirect()->route('admin.pages.index')->with('success', __('admin.page_updated'));
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', __('admin.page_deleted'));
    }

    protected function handleSeoMetadata($model, Request $request)
    {
        if ($request->has('seo_metadata')) {
            $seoData = $request->seo_metadata;
            $seo = $model->seoMetadata()->firstOrCreate([]);
            
            $metaTitle = is_array($seo->meta_title) ? $seo->meta_title : [];
            $metaTitle[app()->getLocale()] = $seoData['meta_title'] ?? null;
            
            $metaDesc = is_array($seo->meta_description) ? $seo->meta_description : [];
            $metaDesc[app()->getLocale()] = $seoData['meta_description'] ?? null;
            
            $seo->meta_title = $metaTitle;
            $seo->meta_description = $metaDesc;
            
            if ($request->hasFile('seo_metadata.og_image')) {
                $seo->og_image = $request->file('seo_metadata.og_image')->store('seo', 'public');
            }
            
            $seo->save();
        }
    }
}
