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

        $model->fill($version->data);
        $model->withoutEvents(function() use($model) {
            $model->save();
        });
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
        return VersionHistory::where('commit_id', $commitId)->first();
    }

    /**
     * Get the differences between two versions of a model.
     *
     * @param Model $model The model instance.
     * @param string $commitId1 The commit ID of the first version.
     * @param string $commitId2 The commit ID of the second version.
     *
     * @return array The differences between the two versions.
     *
     * @throws \RuntimeException If one or both versions are not found.
     */
    public static function getVersionDiff(Model $model, string $commitId1, string $commitId2): array
    {
        $version1 = $model->versionHistory()->where('commit_id', $commitId1)->first();
        $version2 = $model->versionHistory()->where('commit_id', $commitId2)->first();

        if (!$version1 || !$version2) {
            throw new \InvalidArgumentException("One or both versions not found.");
        }

        $data1 = is_string($version1->data) ? json_decode($version1->data, true) : $version1->data;
        $data2 = is_string($version2->data) ? json_decode($version2->data, true) : $version2->data;

        $diff = [];

        foreach ($data1 as $key => $value) {
            if (array_key_exists($key, $data2) && $data2[$key] !== $value) {
                $diff[] = [
                    'field_name' => $key,
                    'old_value' => $value,
                    'new_value' => $data2[$key],
                ];
            }
        }

        return $diff;
    }

    /**
     * Revert the model to its last modified version.
     *
     * @param Model $model The model instance.
     *
     * @return void
     *
     * @throws \RuntimeException If the version history is empty.
     */
    public static function restoreToLastVersion(Model $model): void
    {
        $latestVersion = $model->versionHistory()
            ->where('created_at', '<', $model->updated_at)
            ->latest()
            ->first();

        if (!$latestVersion) {
            throw new \InvalidArgumentException("No version history exists for this model.");
        }

        $model->revertToVersion($latestVersion->commit_id);
    }
}
