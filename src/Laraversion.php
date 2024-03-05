<?php

namespace Laraversion\Laraversion;

use Illuminate\Support\Facades\App;
use Laraversion\Laraversion\Models\VersionHistory;

class Laraversion
{
    /**
     * Get the version history for a given model.
     *
     * @param string $modelClass The class name of the model.
     *
     * @return array The version history as an array.
     */
    public static function getVersionHistory(string $modelClass): array
    {
        $model = App::make($modelClass);

        return $model->versionHistory()->get()->toArray();
    }

    /**
     * Restore a previous version of a given model.
     *
     * @param string $modelClass The class name of the model.
     * @param string $commitId The commit ID of the version to restore.
     *
     * @return void
     *
     * @throws \RuntimeException If the version is not found.
     */
    public static function restoreVersion(string $modelClass, string $commitId): void
    {
        $model = App::make($modelClass);
        $version = $model->versionHistory()->where('commit_id', $commitId)->first();

        if (!$version) {
            throw new \RuntimeException("Version not found for commit ID {$commitId}");
        }

        $model->fill(json_decode($version->data, true));
        $model->save();
    }

    /**
     * Get the latest version of a given model.
     *
     * @param string $modelClass The class name of the model.
     *
     * @return VersionHistory|null The latest version or null if not found.
     */
    public static function getLatestVersion(string $modelClass): ?VersionHistory
    {
        $model = App::make($modelClass);

        return $model->versionHistory()->latest()->first();
    }

    /**
     * Get all versions of a given model.
     *
     * @param string $modelClass The class name of the model.
     *
     * @return \Illuminate\Database\Eloquent\Collection|VersionHistory[] The versions of the model.
     */
    public static function getAllVersions(string $modelClass): \Illuminate\Database\Eloquent\Collection
    {
        $model = App::make($modelClass);

        return $model->versionHistory()->get();
    }

    /**
     * Get a specific version of a given model.
     *
     * @param string $modelClass The class name of the model.
     * @param string $commitId The commit ID of the version.
     *
     * @return VersionHistory|null The version or null if not found.
     */
    public static function getVersion(string $modelClass, string $commitId): ?VersionHistory
    {
        $model = App::make($modelClass);

        return $model->versionHistory()->where('commit_id', $commitId)->first();
    }

    /**
     * Get the version history for a given commit ID.
     *
     * @param string $commitId The commit ID.
     *
     * @return \Illuminate\Database\Eloquent\Collection|VersionHistory[] The versions with the given commit ID.
     */
    public static function getVersionHistoryByCommitId(string $commitId): \Illuminate\Database\Eloquent\Collection
    {
        return VersionHistory::where('commit_id', $commitId)->get();
    }
}
