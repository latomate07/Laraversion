Laraversion
===========

Laraversion is a Laravel package that simplifies version management for your Eloquent models. It allows you to easily track and restore previous versions of your data using a Git-inspired versioning system.

Table of Contents
-----------------

1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Usage](#usage)
    * [Using the Trait](#using-the-trait)
    * [Available Methods](#available-methods)
    * [Listening to Events](#listening-to-events)
    * [Facade Usage](#facade-usage)
    * [Examples](#examples)
5. [Use Cases](#use-cases)
6. [Commands](#commands)
    * [List all versions](#list-all-versions)
    * [Restore a specific version](#restore-specific-version)
    * [Compare two versions](#compare-two-versions)
7. [Configuration](#configuration)
    * [Excluding Attributes from Versioning](#excluding-attributes)
8. [Contribution](#contribution)
9. [License](#license)

<a name="features"></a>
Features
--------

* Automatic version tracking for Eloquent models.
* Easy restoration of previous versions.
* Support for Laravel model events (created, updated, deleted, restored, forceDeleted).
* Storage of version data in a dedicated table with unique UUIDs.
* Easy configuration of the maximum number of versions to retain.
* Events for version creation, pruning, and restoration.
* Ability to specify which model attributes to exclude from versioning.

<a name="requirements"></a>
Requirements
------------

* PHP ^8.1
* Laravel ^9.0

<a name="installation"></a>
Installation
------------

1. Install the package via Composer:
```
composer require laraversion/laraversion
```
2. Publish the package configuration & migration file:
```css
php artisan vendor:publish --provider="Laraversion\Laraversion\LaraversionServiceProvider"
```
3. Run the migrations:
```
php artisan migrate
```
<a name="usage"></a>
Usage
-----

### <a name="using-the-trait"></a>Using the Trait

To use Laraversion in your models, add the `Laraversion\Laraversion\Traits\Versionable` trait:
```php
use Laraversion\Laraversion\Traits\Versionable;

class YourModel extends Model
{
    use Versionable;
}
```

### <a name="available-methods"></a>Available Methods

When using the Versionable trait in your model, the following methods are available:

1. `versionHistory()`: Get the version history for a given model.
2. `recordVersion(VersionEventType $eventType)`: Record a new version for the model.
3. `revertToVersion(string $commitId)`: Revert the model to a specific version.
4. `resetToLastVersion()`: Revert the model to its last modified version.
5. `resetToVersionAtDate(Carbon $date)`: Revert the model to the version at a specific date.
6. `getVersionDiff(string $commitId1, string $commitId2)`: Get the differences between two versions of a model.

### <a name="listening-to-events"></a>Listening to Events

Laraversion fires events when specific actions occur, allowing you to perform additional processing or custom actions. To listen to these events, you can create event listeners and register them in your application.

Here are the events fired by Laraversion:

- `VersionCreatedEvent`: Triggered when a new version is created for a model.
- `VersionPrunedEvent`: Triggered when an old version is deleted to maintain the maximum number of versions.
- `VersionRestoredEvent`: Triggered when a soft-deleted model is restored.

To create an event listener, you can create a file like the following:
```php
<?php

namespace App\Listeners;

use Laraversion\Laraversion\Events\VersionCreatedEvent;

class VersionCreated
{
    public function handle(VersionCreatedEvent $event)
    {
        // Access the VersionHistory model using $event->version
    }
}
```
Then, you must register the events you want to act on in your `App\Providers\EventServiceProvider`'s `$listen` array:
```php
protected $listen = [
    'Laraversion\Laraversion\Events\VersionCreatedEvent' => [
        'App\Listeners\VersionCreated',
    ],
    // ...
```

### <a name="facade-usage"></a>Facade Usage

You can use the Laraversion facade to interact with your models' versions by importing the facade:
```php
use Laraversion\Laraversion\Facades\Laraversion;
```
Then, you can use the facade's methods:

1. `Laraversion::getVersionHistory(Model $model)`: Get the version history for a given model instance.
2. `Laraversion::restoreVersion(Model $model, string $commitId)`: Restore a previous version of a given model instance.
3. `Laraversion::restoreToLastVersion(Model $model)`: Revert the given model to its last modified version before current one.
4. `Laraversion::getLatestVersion(Model $model)`: Get the latest version of a given model instance.
5. `Laraversion::getAllVersions()`: Get all versions of all models.
6. `Laraversion::getVersion(Model $model, string $commitId)`: Get a specific version of a given model instance.
7. `Laraversion::getVersionDiff(Model $model, string $commitId1, string $commitId2)`: Get the differences between two versions of a given model instance.

<a name="examples"></a>
Examples
--------

### Example 1: Get version history for a User model instance using Laraversion Facade
```php
use App\Models\User;
use Laraversion\Laraversion\Facades\Laraversion;

$user = User::first();
$versionHistory = Laraversion::getVersionHistory($user);
```

### Alternatively Get version history for a User model instance using trait methods

```php
use App\Models\User;

$user = User::first();
$versionHistory = $user->versionHistory()->get();
// or
$versionHistory = $user->versionHistory;
```

### Example 2: Restore a previous version of a Post model instance using Laraversion Facade
```php
use App\Models\Post;
use Laraversion\Laraversion\Facades\Laraversion;

$post = Post::firstWhere('slug', 'my-awesome-blog-post');
Laraversion::restoreVersion($post, '123e4567-e89b-12d3-a456-426614174000');
```

### Alternatively Restore a previous version of a Post model instance using trait methods
```php
use App\Models\Post;

$post = Post::firstWhere('slug', 'my-awesome-blog-post');
$post->revertToVersion('123e4567-e89b-12d3-a456-426614174000');
```

<a name="use-cases"></a>
Use Cases
---------

1. **Audit actions:** Use Laraversion to track changes made in your application, allowing you to maintain a comprehensive audit log for compliance and security purposes.
2. **Collaborative content editing:** Use Laraversion to manage content revisions in a collaborative environment, providing a seamless way to track and revert changes made by multiple authors.
3. **Rollback faulty updates:** Use Laraversion to quickly revert to a stable version of your data if an update causes unexpected issues, minimizing downtime and ensuring data integrity.

<a name="commands"></a>
Commands
--------

Laraversion provides Artisan commands to manage the versions of your models.

### <a name="list-all-versions"></a>List all versions of your app models:

```javascript
php artisan laraversion list
```

### <a name="restore-specific-version"></a>Restore a specific version of a model:

```javascript
php artisan laraversion:restore {model} {commit_id}
```

### <a name="compare-two-versions"></a>Compare two versions of a model:

```javascript
php artisan laraversion:compare {model} {commit_id1} {commit_id2}
```
Replace `{model}` with the model class name and `{commit_id1}` and `{commit_id2}` with the UUIDs of the versions you want to compare. This command will display a table showing the differences between the two versions.

Example:

```javascript
php artisan laraversion:compare Post 123e4567-e89b-12d3-a456-426614174000 7890abcd-efgh-3456-ijkl-mnopqrstuvwx
```
This command will compare the versions of the `Post` model with the UUIDs `123e4567-e89b-12d3-a456-426614174000` and `7890abcd-efgh-3456-ijkl-mnopqrstuvwx`, and display a table showing the differences between the two versions.

Note: Make sure to replace `Post`, `123e4567-e89b-12d3-a456-426614174000`, and `7890abcd-efgh-3456-ijkl-mnopqrstuvwx` with the actual model class name and version UUIDs you want to compare.

You can find the UUIDs of the available versions using the `php artisan laraversion` command.

<a name="configuration"></a>
Configuration
-------------

You can configure the maximum number of versions to retain and the events to listen for versioning in the `config/laraversion.php` configuration file.

Here are the keys you can find in the configuration file:

* `max_versions`: The maximum number of versions to keep for each model.
* `listen_events`: The events to listen for versioning on all models.
* `models`: The models to version and their specific configuration.

For example, you can set the maximum number of versions to retain for a specific model like this:

```php
'models' => [
    'App\Models\YourModel' => [
        'max_versions' => 5,
        'listen_events' => [
            'created',
            'updated',
            // more events here ...
        ],
    ],
],
```

<a name="excluding-attributes"></a>
### Excluding Attributes from Versioning

To exclude specific attributes from versioning, define a `$untrackedFields` property in your model class:
```php
use Laraversion\Laraversion\Traits\Versionable;

class YourModel extends Model
{
    use Versionable;

    /**
     * The attributes that should not be tracked by the versioning system.
     *
     * @var array
     */
    protected $untrackedFields = ['remember_token', 'email_verified_at'];
}
```
In this example, the `remember_token` and `email_verified_at` attributes will not be tracked by Laraversion.

<a name="contribution"></a>
Contribution
------------

Contributions are welcome!

<a name="license"></a>
License
-------

Laraversion is open source and released under the MIT license.