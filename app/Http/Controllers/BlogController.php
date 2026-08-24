<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $isFirstPage = $request->get('page', 1) == 1;
        
        $featuredPost = null;
        $latestPosts = collect();
        $archivePosts = collect();
        $locale = app()->getLocale();

        foreach ($posts->items() as $post) {
            if (empty($post->title[$locale])) continue;
            
            if ($isFirstPage) {
                if (!$featuredPost) {
                    $featuredPost = $post;
                } elseif ($latestPosts->count() < 3) {
                    $latestPosts->push($post);
                } else {
                    $archivePosts->push($post);
                }
            } else {
                $archivePosts->push($post);
            }
        }

        return view('blog.index', compact('posts', 'featuredPost', 'latestPosts', 'archivePosts', 'isFirstPage'));
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
