<?php

namespace Laraversion\Laraversion\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laraversion\Laraversion\Enums\VersionEventType;
use Laraversion\Laraversion\Models\VersionHistory;

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

        static::restored(function (Model $model) {
            $model->recordVersion(VersionEventType::RESTORED);
        });

        static::forceDeleted(function (Model $model) {
            $model->recordVersion(VersionEventType::FORCE_DELETED);
        });
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

        $this->versionHistory()->create($versionData);
        $this->updateCurrentVersion($versionData);
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
     * Update the current version of the model.
     *
     * @param array $versionData
     */
    protected function updateCurrentVersion(array $versionData)
    {
        $this->update([
            'current_version' => $versionData['commit_id'],
        ]);
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
}
