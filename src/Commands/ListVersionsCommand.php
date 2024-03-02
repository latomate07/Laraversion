<?php

namespace Laraversion\Laraversion\Commands;

use Illuminate\Console\Command;
use Laraversion\Laraversion\Models\VersionHistory;

class ListVersionsCommand extends Command
{
    public $signature = 'laraversion list';

    public $description = 'List the latest versions of models';

    public function handle(): int
    {
        $versions = VersionHistory::with('versionable')
            ->latest()
            ->get();

        $headers = ['Model Type', 'Date', 'Commit ID'];

        $data = [];

        foreach ($versions as $version) {
            $data[] = [
                'Model Type' => get_class($version->versionable),
                'Date' => $version->created_at->format('Y-m-d H:i:s'),
                'Commit ID' => $version->commit_id,
            ];
        }

        $this->table($headers, $data);

        return self::SUCCESS;
    }
}
