<?php

namespace Laraversion\Laraversion\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laraversion\Laraversion\Models\VersionHistory;

class LaraversionController extends Controller
{
    /**
     * Display a listing of the available models.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $versions = VersionHistory::latest()->get();
        return view('laraversion::index', [
            'models' => $this->getModels(),
            'versions' => $versions,
        ]);
    }

    /**
     * Get a list of the available models.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getModels()
    {
        $models = VersionHistory::select('versionable_type')->distinct()->get();
        return $models;
    }

    /**
     * Get a listing of the versions for a specific model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersions(Request $request)
    {
        $this->validate($request, [
            'versionable_type' => 'required|string',
            'event_type' => 'nullable|string',
        ]);

        $query = VersionHistory::where('versionable_type', $request->versionable_type);

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        $versions = $query->latest()->get();

        return response()->json($versions);
    }
}
