<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Traits\LogsAdminActivity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use LogsAdminActivity;

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::select('id', 'name_en', 'name_ar', 'name_de', 'name_es', 'name_tr', 'name_zh', 'icon', 'image', 'status', 'slug', 'disk');

        // Smart Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_de', 'like', "%{$search}%")
                  ->orWhere('name_es', 'like', "%{$search}%")
                  ->orWhere('name_tr', 'like', "%{$search}%")
                  ->orWhere('name_zh', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && in_array($request->status, ['active', 'pending'])) {
            $query->where('status', $request->status);
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.categories._list', compact('categories'))->render();
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $data = $request->validated();
        
        // Slug generation
        $data['slug'] = $data['slug'] ?? Str::slug($data['name_en']);
        
        // Image handling
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'r2');
            $data['disk'] = 'r2';
        }

        $category = Category::create($data);
        
        $this->handleSeoMetadata($category, $request);
        
        $this->logAdminAction('category_created', $category);

        return back()->with('success', __('admin.saved_successfully'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);

        $data = $request->validated();
        
        // Slug generation
        $data['slug'] = $data['slug'] ?? Str::slug($data['name_en']);
        
        // Image handling
        if ($request->hasFile('image')) {
            if ($category->image && !str_starts_with($category->image, 'http')) {
                Storage::disk($category->disk ?? 'public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'r2');
            $data['disk'] = 'r2';
        }

        $category->update($data);
        
        $this->handleSeoMetadata($category, $request);
        
        $this->logAdminAction('category_updated', $category);

        return back()->with('success', __('admin.saved_successfully'));
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        try {
            if ($category->image && !str_starts_with($category->image, 'http')) {
                Storage::disk($category->disk ?? 'public')->delete($category->image);
            }
            
            $category->delete();
            $this->logAdminAction('category_deleted', $category);
            return back()->with('success', __('admin.deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('admin.cant_delete_dependency'));
        }
    }

    public function updateStatus(\Illuminate\Http\Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $request->validate([
            'status' => 'required|in:active,pending'
        ]);

        $category->update(['status' => $request->status]);
        $this->logAdminAction('category_updated', $category);

        return response()->json([
            'success' => true,
            'message' => __('admin.saved_successfully')
        ]);
    }

    protected function handleSeoMetadata($model, \Illuminate\Http\Request $request)
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
