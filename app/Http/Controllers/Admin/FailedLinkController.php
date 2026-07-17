<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FailedLink;

class FailedLinkController extends Controller
{
    public function index()
    {
        $failedLinks = FailedLink::orderBy('hits', 'desc')->paginate(20);
        return view('admin.seo.failed_links.index', compact('failedLinks'));
    }

    public function destroy(FailedLink $failedLink)
    {
        $failedLink->delete();
        return redirect()->back()->with('success', __('admin.deleted_successfully'));
    }
}
