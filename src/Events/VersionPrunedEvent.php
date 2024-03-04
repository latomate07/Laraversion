<?php

namespace Laraversion\Laraversion\Events;

use Laraversion\Laraversion\Models\VersionHistory;
use Illuminate\Queue\SerializesModels;

/**
 * Class VersionPrunedEvent
 *
 * @package Laraversion\Laraversion\Events
 *
 * @property VersionHistory $version
 */
class VersionPrunedEvent
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