<?php
namespace Laraversion\Laraversion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laraversion\Laraversion\LaraversionServiceProvider
 */
class Laraversion extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Laraversion\Laraversion\Laraversion::class;
    }
}
