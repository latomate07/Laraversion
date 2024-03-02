<?php

namespace Laraversion\Laraversion\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\VersionHistory;

class LaraversionCommand extends Command
{
    public $signature = 'laraversion restore {model} {commit_id}';

    public $description = 'Restore a specific version of a model';

    public function handle(): int
    {
        $modelName = $this->argument('model');
        $commitId = $this->argument('commit_id');

        $versionHistory = VersionHistory::where('commit_id', $commitId)->first();

        if (!$versionHistory) {
            $this->error('Version not found.');
            return self::FAILURE;
        }

        $modelClass = 'App\\Models\\' . ucfirst($modelName);

        if (!class_exists($modelClass)) {
            $this->error('Model not found.');
            return self::FAILURE;
        }

        $model = $modelClass::find($versionHistory->versionable_id);

        if (!$model) {
            $this->error('Model instance not found.');
            return self::FAILURE;
        }

        $data = json_decode($versionHistory->data, true);

        DB::transaction(function () use ($model, $data) {
            foreach ($data as $column => $value) {
                $model->{$column} = $value;
            }
            $model->save();
        });

        $this->info("Model {$modelName} restored to version {$commitId}.");

        return self::SUCCESS;
    }
}
