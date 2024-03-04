<?php

namespace Laraversion\Laraversion\Commands;

use Illuminate\Console\Command;
use Laraversion\Laraversion\Models\VersionHistory;

class CompareVersionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'laraversion:compare {model} {commit_id1} {commit_id2}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Compare two versions of a model';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $modelName = $this->argument('model');
        $commitId1 = $this->argument('commit_id1');
        $commitId2 = $this->argument('commit_id2');

        $versionHistory1 = VersionHistory::where('commit_id', $commitId1)->first();
        $versionHistory2 = VersionHistory::where('commit_id', $commitId2)->first();

        if (!$versionHistory1 || !$versionHistory2) {
            $this->error('One or both versions not found.');
            return self::FAILURE;
        }

        $modelClass = 'App\\Models\\' . ucfirst($modelName);

        if (!class_exists($modelClass)) {
            $this->error('Model not found.');
            return self::FAILURE;
        }

        $data1 = json_decode($versionHistory1->data, true);
        $data2 = json_decode($versionHistory2->data, true);

        $diff = $this->getDiff($data1, $data2);

        if (empty($diff)) {
            $this->info('No differences found between the two versions.');
        } else {
            $this->table(['Attribute', 'Version 1', 'Version 2'], $diff);
        }

        return self::SUCCESS;
    }

    /**
     * Get the differences between two arrays.
     *
     * @param array $data1 The first array to compare.
     * @param array $data2 The second array to compare.
     *
     * @return array The differences between the two arrays.
     */
    private function getDiff(array $data1, array $data2): array
    {
        $diff = [];

        foreach ($data1 as $key => $value) {
            if (!array_key_exists($key, $data2) || $data2[$key] !== $value) {
                $diff[] = [$key, $value, $data2[$key] ?? 'N/A'];
            }
        }

        foreach ($data2 as $key => $value) {
            if (!array_key_exists($key, $data1)) {
                $diff[] = [$key, 'N/A', $value];
            }
        }

        return $diff;
    }
}
