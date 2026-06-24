<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\LogsAdminActivity;

class UserController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.users._users_list', compact('users'))->render();
        }

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('update', clone $user); // Using update policy for role change

        if (auth()->id() === $user->id) {
            return response()->json(['success' => false, 'message' => __('admin.cant_change_own_role')], 403);
        }

        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);
        $this->logAdminAction('user_role_updated', $user);

        return response()->json([
            'success' => true, 
            'message' => __('admin.saved_successfully'),
            'new_role' => $user->role
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            $msg = __('admin.cant_delete_yourself');
            return request()->ajax() 
                ? response()->json(['success' => false, 'message' => $msg], 403)
                : back()->with('error', $msg);
        }

        try {
            $user->delete();
            $this->logAdminAction('user_deleted', $user);
            $msg = __('admin.deleted_successfully');
            
            return request()->ajax()
                ? response()->json(['success' => true, 'message' => $msg])
                : back()->with('success', $msg);
        } catch (\Exception $e) {
            $msg = __('admin.cant_delete_dependency');
            return request()->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }
}
