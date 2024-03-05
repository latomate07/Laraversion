<?php

namespace Laraversion\Laraversion\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laraversion\Laraversion\Enums\VersionEventType;
use Laraversion\Laraversion\Models\VersionHistory;
use Laraversion\Laraversion\Events\VersionCreatedEvent;
use Laraversion\Laraversion\Events\VersionPrunedEvent;
use Laraversion\Laraversion\Events\VersionRestoredEvent;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

trait Versionable
{
    /**
     * Boot the versionable trait.
     */
    public static function bootVersionable()
    {
        // Get the events to listen to for the current model
        $listenEvents = (new static)->getListenEvents();

        // Define event handlers for each event type
        $eventHandlers = [
            'created'   => function (Model $model) {
                $model->recordVersion(VersionEventType::CREATED);
            },
            'updated'   => function (Model $model) {
                $model->recordVersion(VersionEventType::UPDATED);
            },
            'deleted'   => function (Model $model) {
                $model->recordVersion(VersionEventType::DELETED);
            },
            'restored'  => function (Model $model) {
                $model->recordVersion(VersionEventType::RESTORED);
                event(new VersionRestoredEvent($model));
            },
            'forceDeleted' => function (Model $model) {
                $model->recordVersion(VersionEventType::FORCE_DELETED);
            },
        ];

        foreach ($listenEvents as $event) {
            // If the event handler is not set, skip to the next iteration
            if (!isset($eventHandlers[$event])) {
                continue;
            }

            $handler = $eventHandlers[$event];

            // Check if the current event requires the model to use the SoftDeletes trait
            if ($event === 'restored' || $event === 'forceDeleted') {
                if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
                    // Add the event handler to the current event
                    static::$event($handler);
                }
            } else {
                // Add the event handler to the current event
                static::$event($handler);
            }
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
            $version = $this->versionHistory()->create($versionData);
            event(new VersionCreatedEvent($version));
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
            event(new VersionPrunedEvent($oldestVersion));
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
     * Get the events to listen for versioning.
     *
     * @return array
     */
    protected function getListenEvents(): array
    {
        $modelClass = get_class($this);
        $modelConfig = config("laraversion.models.{$modelClass}", []);
        $listenEvents = array_merge(config('laraversion.listen_events', []), $modelConfig['listen_events'] ?? []);

        return $listenEvents;
    }

    /**
     * Revert the model to a specific version.
     *
     * @param string $commitId The commit ID of the version to revert to.
     * @throws \InvalidArgumentException If the specified version is not found.
     */
    public function revertToVersion(string $commitId): void
    {
        $version = $this->versionHistory()->where('commit_id', $commitId)->first();

        if (!$version) {
            throw new \InvalidArgumentException("No version found with commit ID '$commitId'.");
        }

        // Get the data from the version
        $versionData = json_decode($version->data, true);

        // Update the model's attributes with the version data
        $this->fill($versionData);
        $this->save();
    }

    /**
     * Revert the model to its last modified version.
     *
     * @throws \InvalidArgumentException If no version history exists.
     */
    public function resetToLastVersion(): void
    {
        $latestVersion = $this->versionHistory()
            ->where('created_at', '<', $this->updated_at)
            ->latest()
            ->first();

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
