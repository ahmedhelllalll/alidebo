<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\LogsAdminActivity;
use App\Notifications\ProductApprovedNotification;
use App\Notifications\ProductRejectedNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use LogsAdminActivity, AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::with([
            'businessProfile:id,name,slug,owner_id',
            'businessProfile.owner:id,name,email',
            'translations',
            'category:id,name_en,name_ar'
        ])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            })->orWhereHas('businessProfile', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(15);

        return response()->json(['products' => $products]);
    }

    public function updateStatus(Request $request, Product $product)
    {
        $this->authorize('approve', $product);

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        $newStatus = $request->status;

        if ($newStatus === 'approved') {
            $product->update([
                'status' => 'approved',
                'rejection_reason' => null
            ]);
            
            $owner = $product->businessProfile->owner;
            if ($owner) {
                $owner->notify(new ProductApprovedNotification($product));
            }
        } elseif ($newStatus === 'rejected') {
            $product->update([
                'status' => 'rejected',
                'rejection_reason' => $request->admin_notes
            ]);
            
            $owner = $product->businessProfile->owner;
            if ($owner) {
                $owner->notify(new ProductRejectedNotification($product, $request->admin_notes ?? __('admin.no_reason_provided')));
            }
        } else {
            $product->update(['status' => 'pending']);
        }

        $this->logAdminAction('product_status_updated', $product);

        return response()->json([
            'success' => true,
            'message' => __('admin.status_updated'),
            'new_status' => $newStatus
        ]);
    }
}
