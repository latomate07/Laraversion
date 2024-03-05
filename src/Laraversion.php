<?php

namespace Laraversion\Laraversion;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Laraversion\Laraversion\Models\VersionHistory;

class Laraversion
{
    /**
     * Get the version history for a given model.
     *
     * @param Model $model The model instance.
     *
     * @return Collection The version history as a collection.
     */
    public static function getVersionHistory(Model $model): Collection
    {
        return $model->versionHistory()->get();
    }

    /**
     * Restore a previous version of a given model.
     *
     * @param Model $model The model instance.
     * @param string $commitId The commit ID of the version to restore.
     *
     * @return void
     *
     * @throws \RuntimeException If the version is not found.
     */
    public static function restoreVersion(Model $model, string $commitId): void
    {
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
     * @param Model $model The model instance.
     *
     * @return VersionHistory|null The latest version or null if not found.
     */
    public static function getLatestVersion(Model $model): ?VersionHistory
    {
        return $model->versionHistory()->latest()->first();
    }

    /**
     * Get all versions of a given model.
     *
     * @return \Illuminate\Database\Eloquent\Collection|VersionHistory[] The versions of all models.
     */
    public static function getAllVersions(): \Illuminate\Database\Eloquent\Collection
    {
        return VersionHistory::all();
    }

    /**
     * Get a specific version of a given model.
     *
     * @param Model $model The model instance.
     * @param string $commitId The commit ID of the version.
     *
     * @return VersionHistory|null The version or null if not found.
     */
    public static function getVersion(Model $model, string $commitId): ?VersionHistory
    {
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