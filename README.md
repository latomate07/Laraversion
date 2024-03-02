# Laraversion

Laraversion is a Laravel package that simplifies version management for your Eloquent models. It allows you to easily track and restore previous versions of your data using a Git-inspired versioning system.

## Features

- Automatic version tracking for Eloquent models.
- Easy restoration of previous versions.
- Support for Laravel model events (created, updated, deleted, restored, forceDeleted).
- Storage of version data in a dedicated table with unique UUIDs.
- Easy configuration of the maximum number of versions to retain.

## Installation

1. Install the package via Composer:
```
composer require laraversion/laraversion
```
2. Publish the package configuration & migration file:
```css
php artisan vendor:publish --provider="Laraversion\Laraversion\LaraversionServiceProvider" --tag=config
php artisan vendor:publish --provider="Laraversion\Laraversion\LaraversionServiceProvider" --tag=migrations
```
3. Run the migrations:
```
php artisan migrate
```
## Usage

To use Laraversion in your models, add the `Laraversion\Laraversion\Traits\Versionable` trait:
```php
use Laraversion\Laraversion\Traits\Versionable;

class YourModel extends Model
{
    use Versionable;
}
```
### Available Methods

When using the Versionable trait in your model, the following methods are available:

1. `versionHistory()`: Get the version history for a given model.
2. `recordVersion(VersionEventType $eventType)`: Record a new version for the model.
3. `revertToVersion(string $commitId)`: Revert the model to a specific version.
4. `resetToLastVersion()`: Revert the model to its last modified version.
5. `resetToVersionAtDate(Carbon $date)`: Revert the model to the version at a specific date. (ex: User::find(1)->resetToVersionAtDate(Carbon::yesterday()))

## Use Cases

1. **Track changes in user profiles:** Use Laraversion to track changes made to user profiles in your application, allowing you to easily revert to previous versions if necessary.
2. **Audit content updates:** Use Laraversion to audit content updates in a CMS, providing a history of changes and the ability to restore previous versions.
3. **Monitor product updates:** Use Laraversion to monitor product updates in an e-commerce platform, ensuring you can revert to previous versions if an update causes issues.

## Commands

Laraversion provides an Artisan command to restore a specific version of a model.

1. `List all versions of your app models:`
```javascript
php artisan laraversion
```
This will list out all the versions of your application models.

2. `Restore a specific version of a model:`
```javascript
php artisan laraversion:restore {model} {commit_id}
```
Replace `{model}` with the model class name and `{commit_id}` with the UUID of the version you want to restore.

## Configuration

You can configure the maximum number of versions to retain by changing the `max_versions` value in the `config/laraversion.php` configuration file.

## Contribution

Contributions are welcome!

## License

Laraversion is open source and released under the MIT license.