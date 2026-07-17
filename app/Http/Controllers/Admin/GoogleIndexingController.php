<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleIndexingController extends Controller
{
    /**
     * Handle manual request for Google Indexing.
     */
    public function requestIndexing(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'indexable_type' => 'required|string',
            'indexable_id' => 'required|integer',
        ]);

        $log = \App\Models\GoogleIndexLog::updateOrCreate(
            [
                'indexable_type' => $request->indexable_type,
                'indexable_id' => $request->indexable_id,
            ],
            [
                'url' => $request->url,
                'status' => 'pending',
                'response' => null
            ]
        );

        \App\Jobs\SubmitUrlToGoogleIndex::dispatch($log);

        return redirect()->back()->with('success', __('admin.indexing_request_submitted'));
    }
}
