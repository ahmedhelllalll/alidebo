<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $hreflangs = [];
        $locales = ['en', 'ar', 'es', 'de', 'zh', 'tr'];
        
        foreach ($locales as $loc) {
            if (isset($post->title[$loc]) && !empty($post->title[$loc])) {
                $hreflangs[$loc] = url('/' . $loc . '/blog/' . $post->slug);
            }
        }
        
        view()->share('hreflangs', $hreflangs);

        return view('blog.show', compact('post'));
    }
}
