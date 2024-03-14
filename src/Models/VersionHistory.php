<?php

namespace Laraversion\Laraversion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VersionHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relationships that should be eager loaded by default with this model.
     *
     * @var array
     */
    protected $with = ['versionable'];

    /**
     * Get the versionable model.
     *
     * @return MorphTo
     */
    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the latest version for a given model.
     *
     * @param Model $model
     * @return static|null
     */
    public static function getLatestVersion(Model $model): ?self
    {
        return $model->versionHistory()->latest()->first();
    }

    /**
     * Get all versions for a given model.
     *
     * @param Model $model
     * @return Collection
     */
    public static function getAllVersions(Model $model): Collection
    {
        return $model->versionHistory()->get();
    }

    /**
     * Get a specific version for a given model.
     *
     * @param Model $model
     * @param string $commitId
     * @return static|null
     */
    public static function getVersion(Model $model, string $commitId): ?self
    {
        return $model->versionHistory()->where('commit_id', $commitId)->first();
    }

    /**
     * Get the version history for a given commit ID.
     *
     * @param string $commitId
     * @return Collection
     */
    public static function getVersionHistoryByCommitId(string $commitId): Collection
    {
        return self::where('commit_id', $commitId)->get();
    }
}
