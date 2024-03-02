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
1. Publish the package configuration:
```css
php artisan vendor:publish --provider="Laraversion\Laraversion\LaraversionServiceProvider" --tag=config
```
1. Run the migrations:
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
## Commands

Laraversion provides an Artisan command to restore a specific version of a model:
```javascript
php artisan laraversion:restore {model} {commit_id}
```
Replace `{model}` with the model class name and `{commit_id}` with the UUID of the version you want to restore.

## Configuration

You can configure the maximum number of versions to retain by changing the `max_versions` value in the `config/laraversion.php` configuration file.

## Contribution

Contributions are welcome! If you'd like to contribute to Laraversion, please refer to our contribution guide.

## License

Laraversion is open source and released under the MIT license.

---

Feel free to customize this README to fit your package's specific needs and add additional sections as necessary.