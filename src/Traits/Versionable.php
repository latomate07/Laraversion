<?php

namespace Laraversion\Laraversion\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laraversion\Laraversion\Enums\VersionEventType;
use Laraversion\Laraversion\Models\VersionHistory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

trait Versionable
{
    /**
     * Boot the versionable trait.
     */
    public static function bootVersionable()
    {
        static::created(function (Model $model) {
            $model->recordVersion(VersionEventType::CREATED);
        });

        static::updated(function (Model $model) {
            $model->recordVersion(VersionEventType::UPDATED);
        });

        static::deleted(function (Model $model) {
            $model->recordVersion(VersionEventType::DELETED);
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function (Model $model) {
                $model->recordVersion(VersionEventType::RESTORED);
            });

            static::forceDeleted(function (Model $model) {
                $model->recordVersion(VersionEventType::FORCE_DELETED);
            });
        }
    }

    /**
     * Get the version history for a given model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function versionHistory()
    {
        return $this->morphMany(VersionHistory::class, 'versionable');
    }

    /**
     * Record a new version for the model.
     *
     * @param VersionEventType $eventType
     */
    public function recordVersion(VersionEventType $eventType)
    {
        $commitId = (string) Str::uuid();

        $latestVersion = $this->versionHistory()->latest()->first();

        if ($latestVersion) {
            $this->pruneVersionHistory($latestVersion);
        }

        $versionData = $this->getVersionData($eventType, $commitId);

        $this->withoutEvents(function () use ($versionData) {
            $this->versionHistory()->create($versionData);
        });
    }

    /**
     * Prune the version history to maintain the maximum number of versions.
     *
     * @param VersionHistory $latestVersion
     */
    protected function pruneVersionHistory(VersionHistory $latestVersion)
    {
        $maxVersions = $this->getMaxVersions();

        if ($this->versionHistory()->count() >= $maxVersions) {
            $oldestVersion = $this->versionHistory()->oldest()->first();
            $oldestVersion->delete();
        }
    }

    /**
     * Get the maximum number of versions to keep for the model.
     *
     * @return int
     */
    protected function getMaxVersions(): int
    {
        $modelClass = get_class($this);
        $config = Config::get('laraversion.models', []);

        if (array_key_exists($modelClass, $config)) {
            return $config[$modelClass]['max_versions'] ?? Config::get('laraversion.max_versions', 3);
        }

        return Config::get('laraversion.max_versions', 3);
    }

    /**
     * Get the version data for the model.
     *
     * @param VersionEventType $eventType
     * @param string $commitId
     * @return array
     */
    protected function getVersionData(VersionEventType $eventType, string $commitId): array
    {
        $data = $this->getAttributes();

        return [
            'commit_id' => $commitId,
            'event_type' => $eventType->value,
            'data' => json_encode($data),
        ];
    }

    /**
     * Revert the model to its last modified version.
     *
     * @throws \InvalidArgumentException If no version history exists.
     */
    public function resetToLastVersion(): void
    {
        $latestVersion = $this->versionHistory()->latest()->first();

        if (!$latestVersion) {
            throw new \InvalidArgumentException("No version history exists for this model.");
        }

        $this->revertToVersion($latestVersion->commit_id);
    }

    /**
     * Revert the model to the version at a specific date.
     *
     * @param Carbon $date The date of the version to revert to.
     * @throws \InvalidArgumentException If no version is found at the specified date.
     */
    public function resetToVersionAtDate(Carbon $date): void
    {
        $version = $this->versionHistory()->where('created_at', $date)->first();

        if (!$version) {
            throw new \InvalidArgumentException("No version found at date '$date'.");
        }

        $this->revertToVersion($version->commit_id);
    }
}
