# Digbang / Assets
Manage asset versioning for Laravel 4 projects.

## Usage
Add the service provider to your `app/config/app.php` file:

```php
'providers' => [
    // ...
    Digbang\Assets\AssetsServiceProvider::class,
]
```

Publish the config file:

```
php artisan config:publish digbang/assets
```

Add the assets you want to version to the config file:

```php
return [
    'enabled' => true,
    'lock_path' => storage_path('meta/assets.lock'),
    'assets' => [
        'js/site.min.js',
        'css/site.min.css',
    ]
];
```

### The `vasset` function
To use asset versioning, you need to change your templates and replace the use of Laravel's `asset` 
function with this package `vasset` function.
 
```
<script type="text/javascript" src="{{ vasset('js/site.min.js') }}"></script>
```

### The Asset facade
You may also use the facade to generate the versioned urls. First, add the facade to `app/config/app.php`:

```php
'aliases' => [
    // ...
    'Asset' => Digbang\Assets\Facade\Asset::class,
]
```

Then use the `Asset::asset` function:

```
<link rel="stylesheet" href="{{ Asset::asset('css/site.min.css') }}" />
```
