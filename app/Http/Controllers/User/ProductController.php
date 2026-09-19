<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Jobs\ProcessProductTranslation;
use App\Models\ProductCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $business = Auth::user()->businessProfile;
        if (!$business) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'No business profile found.'], 404);
            }
            return redirect()->route('business.create')->with('error', __('products.create_business_first'));
        }

        $query = Product::where('business_profile_id', $business->id)
            ->with(['translations', 'images', 'category', 'specifications']);

        // Filter: Status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter: Search (Name in translations or SKU)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhereHas('translations', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort options
        switch ($request->get('sort')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['products' => $products]);
        }

        $statusCounts = [
            'all' => Product::where('business_profile_id', $business->id)->count(),
            'approved' => Product::where('business_profile_id', $business->id)->where('status', 'approved')->count(),
            'pending' => Product::where('business_profile_id', $business->id)->where('status', 'pending')->count(),
            'rejected' => Product::where('business_profile_id', $business->id)->where('status', 'rejected')->count(),
        ];

        $categories = ProductCategory::where('status', 'active')->orderBy('order')->get();

        return view('users.products.index', compact('products', 'business', 'categories', 'statusCounts'));
    }


    public function create()
    {
        $this->authorize('create', Product::class);
        $business = Auth::user()->businessProfile;
        if (!$business) {
            return redirect()->route('business.create');
        }
        $categories = ProductCategory::where('status', 'active')->orderBy('order')->get();

        return view('users.products.create', compact('business', 'categories'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $business = Auth::user()->businessProfile;
        $product->load(['translations', 'specifications', 'images']);
        $categories = ProductCategory::where('status', 'active')->orderBy('order')->get();

        return view('users.products.edit', compact('product', 'business', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);
        $business = Auth::user()->businessProfile;
        
        $validated = $request->validated();
        
        $isPackaging = in_array($validated['unit'], ['carton', 'box', 'set']);
        $product = Product::create([
            'business_profile_id' => $business->id,
            'sku' => $validated['sku'] ?? null,
            'unit' => $validated['unit'],
            'units_per_package' => $isPackaging ? ($validated['units_per_package'] ?? null) : null,
            'moq' => $validated['moq'] ?? null,
            'base_price' => $validated['base_price'] ?? null,
            'currency' => $validated['currency'] ?? 'EGP',
            'category_id' => $validated['category_id'] ?? null,
            'status' => 'pending'
        ]);

        if ($request->has('specifications')) {
            $this->productService->syncSpecifications($product, $validated['specifications']);
        }

        if ($request->hasFile('images')) {
            $this->productService->uploadMedia($product, $request->file('images'), true);
        }

        // We will seed the initial translation synchronously so the job has a base string
        if (isset($validated['name']) && is_array($validated['name'])) {
            foreach ($validated['name'] as $locale => $name) {
                if (!empty($name)) {
                    $product->translations()->create([
                        'locale' => $locale,
                        'name' => $name,
                        'short_description' => $validated['short_description'][$locale] ?? null,
                        'full_description' => $validated['full_description'][$locale] ?? null,
                    ]);
                }
            }
        }

        $submittedLocales = isset($validated['name']) && is_array($validated['name']) 
            ? array_keys($validated['name']) 
            : [];
        ProcessProductTranslation::dispatch($product, $submittedLocales);

        session()->flash('success', __('products.success_created'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('products.success_created'),
                'redirect_url' => route('products.index'),
                'product' => $product->load(['translations', 'specifications', 'images'])
            ]);
        }

        return redirect()->route('products.index')->with('success', __('products.success_created'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validated();

        $targetUnit = $validated['unit'] ?? $product->unit;
        $isPackaging = in_array($targetUnit, ['carton', 'box', 'set']);
        $unitsPerPackage = $isPackaging
            ? (array_key_exists('units_per_package', $validated) ? $validated['units_per_package'] : $product->units_per_package)
            : null;

        $product->update([
            'sku' => $validated['sku'] ?? $product->sku,
            'unit' => $targetUnit,
            'units_per_package' => $unitsPerPackage,
            'moq' => $validated['moq'] ?? $product->moq,
            'base_price' => $validated['base_price'] ?? $product->base_price,
            'currency' => $validated['currency'] ?? $product->currency ?? 'EGP',
            'category_id' => $validated['category_id'] ?? $product->category_id,
        ]);

        if ($request->has('specifications')) {
            $this->productService->syncSpecifications($product, $validated['specifications']);
        }

        if ($request->hasFile('images')) {
            $this->productService->uploadMedia($product, $request->file('images'));
        }

        if (isset($validated['name']) && is_array($validated['name'])) {
            foreach ($validated['name'] as $locale => $name) {
                if (!empty($name)) {
                    $product->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $name,
                            'short_description' => $validated['short_description'][$locale] ?? null,
                            'full_description' => $validated['full_description'][$locale] ?? null,
                        ]
                    );
                }
            }
            $submittedLocales = array_keys($validated['name']);
            ProcessProductTranslation::dispatch($product, $submittedLocales);
        }

        session()->flash('success', __('products.success_updated'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('products.success_updated'),
                'redirect_url' => route('products.index'),
                'product' => $product->load(['translations', 'specifications', 'images'])
            ]);
        }

        return redirect()->route('products.index')->with('success', __('products.success_updated'));
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('products.success_deleted')
            ]);
        }

        return redirect()->route('products.index')->with('success', __('products.success_deleted'));
    }

    public function markTourSeen(Request $request)
    {
        $user = Auth::user();
        if ($user && is_null($user->products_tour_seen_at)) {
            $user->update(['products_tour_seen_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'products_tour_seen_at' => $user?->products_tour_seen_at?->toIso8601String()
        ]);
    }
}
