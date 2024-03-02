<?php 
namespace Laraversion\Laraversion\Enums;

enum VersionEventType: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
    case FORCE_DELETED = 'forceDeleted';
}
