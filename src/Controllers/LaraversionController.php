<?php

namespace Laraversion\Laraversion\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laraversion\Laraversion\Models\VersionHistory;
use Laraversion\Laraversion\Facades\Laraversion;

class LaraversionController extends Controller
{
    /**
     * Display a listing of the available models.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $versions = VersionHistory::latest()->paginate(12);
        return view('laraversion::index', [
            'models' => VersionHistory::select('versionable_type')->distinct()->get(),
            'versions' => $versions,
            'events' => VersionHistory::select('event_type')->distinct()->get(),
        ]);
    }

    /**
     * Revert a model to a specific version.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function revert(Request $request)
    {
        /**
         * Validate the request data.
         */
        $request->validate([
            'model_id' => 'required|integer',
            'version_id' => 'required|string',
        ]);

        $modelId = $request->model_id;
        $versionId = $request->version_id;

        /**
         * Find the version history instance or return a 404 error.
         */
        $versionHistory = VersionHistory::where('commit_id', $versionId)->firstOrFail();

        if (!$versionHistory) {
            return response()->json(['message' => 'Version not found'], 400);
        }

        /**
         * Revert the model to the specified version.
         */
        Laraversion::restoreVersion($versionHistory->versionable, $versionId);

        /**
         * Return a success view or redirect to a success page.
         */
        return response()->json(['message' => 'Model reverted successfully']);
    }
}
