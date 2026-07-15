<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::latest()->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function checkSlug(Request $request)
    {
        $slug = $request->slug;
        $ignoreId = $request->ignore;

        $exists = BlogPost::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })->exists();

        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'slug' => 'nullable|string|unique:blog_posts,slug',
            'content' => 'nullable|array',
            'media_type' => 'nullable|in:image,video_embed,video_upload,none',
            'video_url' => 'nullable|string|url',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video_upload' => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:20480',
            'status' => 'required|in:published,draft',
            'media_alt' => 'nullable|array',
            'media_alt.*' => 'nullable|string|max:255',
            'seo_metadata' => 'nullable|array',
        ]);

        $firstTitle = collect($data['title'])->filter()->first() ?? 'untitled-blog';
        $data['slug'] = $data['slug'] ?? Str::slug($firstTitle);
        
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if (isset($data['media_type'])) {
            if ($data['media_type'] === 'image' && $request->hasFile('image_upload')) {
                $data['media_url'] = $request->file('image_upload')->store('blog', 'public');
            } elseif ($data['media_type'] === 'video_embed' && !empty($data['video_url'])) {
                $data['media_type'] = 'video';
                $data['media_url'] = $data['video_url'];
            } elseif ($data['media_type'] === 'video_upload' && $request->hasFile('video_upload')) {
                $data['media_type'] = 'video';
                $data['media_url'] = $request->file('video_upload')->store('blog', 'public');
            } else {
                $data['media_type'] = 'none';
                $data['media_url'] = null;
            }
        }

        $post = BlogPost::create($data);

        $this->handleSeoMetadata($post, $request);

        return redirect()->route('admin.blogs.index')->with('success', __('admin.blog_created'));
    }

    public function edit(BlogPost $blog)
    {
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, BlogPost $blog)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'slug' => 'nullable|string|unique:blog_posts,slug,' . $blog->id,
            'content' => 'nullable|array',
            'media_type' => 'nullable|in:image,video_embed,video_upload,none',
            'video_url' => 'nullable|string|url',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video_upload' => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:20480',
            'status' => 'required|in:published,draft',
            'media_alt' => 'nullable|array',
            'media_alt.*' => 'nullable|string|max:255',
            'seo_metadata' => 'nullable|array',
        ]);

        $firstTitle = collect($data['title'])->filter()->first() ?? 'untitled-blog';
        $data['slug'] = $data['slug'] ?? Str::slug($firstTitle);
        
        if ($data['status'] === 'published' && !$blog->published_at) {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        if (isset($data['media_type'])) {
            if ($data['media_type'] === 'image' && $request->hasFile('image_upload')) {
                if ($blog->media_url) {
                    Storage::disk('public')->delete($blog->media_url);
                }
                $data['media_url'] = $request->file('image_upload')->store('blog', 'public');
            } elseif ($data['media_type'] === 'video_embed' && !empty($data['video_url'])) {
                if ($blog->media_url && !Str::startsWith($blog->media_url, 'http')) {
                    Storage::disk('public')->delete($blog->media_url);
                }
                $data['media_type'] = 'video';
                $data['media_url'] = $data['video_url'];
            } elseif ($data['media_type'] === 'video_upload' && $request->hasFile('video_upload')) {
                if ($blog->media_url) {
                    Storage::disk('public')->delete($blog->media_url);
                }
                $data['media_type'] = 'video';
                $data['media_url'] = $request->file('video_upload')->store('blog', 'public');
            } elseif ($data['media_type'] === 'none') {
                if ($blog->media_url) {
                    Storage::disk('public')->delete($blog->media_url);
                }
                $data['media_url'] = null;
            } else {
                // If it's image or video but no new upload/url is provided, retain existing
                unset($data['media_url']); 
                unset($data['media_type']);
            }
        }

        $blog->update($data);

        $this->handleSeoMetadata($blog, $request);

        return redirect()->route('admin.blogs.index')->with('success', __('admin.blog_updated'));
    }

    public function destroy(BlogPost $blog)
    {
        if ($blog->media_type === 'image' && $blog->media_url) {
            Storage::disk('public')->delete($blog->media_url);
        }
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', __('admin.blog_deleted'));
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
                if ($seo->og_image) {
                    Storage::disk('public')->delete($seo->og_image);
                }
                $seo->og_image = $request->file('seo_metadata.og_image')->store('seo', 'public');
            }
            
            $seo->save();
        }
    }
}
