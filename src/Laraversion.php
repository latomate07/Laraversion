<?php 
namespace Laraversion\Laraversion;

class Laraversion
{
    public static function getVersionHistory(string $modelClass): array
    {
        $model = app($modelClass);
        return $model->versionHistory()->get()->toArray();
    }

    public static function restoreVersion(string $modelClass, string $commitId): void
    {
        $model = app($modelClass);
        $version = $model->versionHistory()->where('commit_id', $commitId)->first();

        if (!$version) {
            throw new \RuntimeException("Version not found for commit ID {$commitId}");
        }

        $model->fill(json_decode($version->data, true));
        $model->save();
    }
}
