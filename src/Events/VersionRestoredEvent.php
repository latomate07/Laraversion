<?php

namespace Laraversion\Laraversion\Events;

use Laraversion\Laraversion\Models\VersionHistory;
use Illuminate\Queue\SerializesModels;

/**
 * Class VersionRestoredEvent
 *
 * @package Laraversion\Laraversion\Events
 *
 * @property VersionHistory $version
 */
class VersionRestoredEvent
{
    use SerializesModels;

    public $version;

    /**
     * Create a new event instance.
     *
     * @param  VersionHistory  $version
     */
    public function __construct(VersionHistory $version)
    {
        $this->version = $version;
    }
}