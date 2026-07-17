<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Redirect;

class RedirectController extends Controller
{
    public function index()
    {
        $redirects = Redirect::latest()->paginate(15);
        return view('admin.seo.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('admin.seo.redirects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_url' => 'required|string|max:255|unique:redirects,source_url',
            'target_url' => 'required|string|max:255',
            'status_code' => 'required|integer|in:301,302',
        ]);

        Redirect::create($request->all());

        return redirect()->route('admin.seo.redirects.index')->with('success', __('admin.saved_successfully'));
    }

    public function edit(Redirect $redirect)
    {
        return view('admin.seo.redirects.edit', compact('redirect'));
    }

    public function update(Request $request, Redirect $redirect)
    {
        $request->validate([
            'source_url' => 'required|string|max:255|unique:redirects,source_url,' . $redirect->id,
            'target_url' => 'required|string|max:255',
            'status_code' => 'required|integer|in:301,302',
        ]);

        $redirect->update($request->all());

        return redirect()->route('admin.seo.redirects.index')->with('success', __('admin.saved_successfully'));
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();
        return redirect()->route('admin.seo.redirects.index')->with('success', __('admin.deleted_successfully'));
    }
}
