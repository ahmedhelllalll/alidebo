<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return redirect()->route('login');
        }

        $businesses = $user->businessProfiles;
        $isEmpty = $businesses->isEmpty();

        return view('user.index', [
            'isEmpty' => $isEmpty,
            'businesses' => $businesses
        ]);
    }
}
