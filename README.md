# Use Glide with ease directly in Laravel views

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lukasmu/laravel-glide.svg)](https://packagist.org/packages/lukasmu/laravel-glide)
[![GitHub Run Tests Action Status](https://img.shields.io/github/actions/workflow/status/lukasmu/laravel-glide/run-tests.yml?branch=main&label=tests)](https://github.com/lukasmu/laravel-glide/actions?query=workflow%3A"Run&nbsp;tests"+branch%3Amain)
[![GitHub Format Code Action Status](https://img.shields.io/github/actions/workflow/status/lukasmu/laravel-glide/format-code.yml?branch=main&label=code%20style)](https://github.com/lukasmu/laravel-glide/actions?query=workflow%3A"Format&nbsp;code"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lukasmu/laravel-glide.svg)](https://packagist.org/packages/lukasmu/laravel-glide)

This package simplifies the use of the [Glide](https://glide.thephpleague.com/) image manipulation library within [Laravel](https://laravel.com) applications.
It provides a secure and efficient way to serve optimized, enhanced, and responsive images with minimal effort.

Key features include:
- A dedicated controller to serve dynamically manipulated images.
- A middleware to prevent unauthorized access and misuse.
- A generator to create valid URLs for invoking the controller.
- A view component for automatically generating responsive `img` elements (i.e. with auto-populated `srcset` attributes).
- An enhanced caching logic for improved performance and fast load times.

## Installation

You can install the package via composer:

```bash
composer require lukasmu/laravel-glide
```

You may publish the ```glide.php``` config file with:

```bash
php artisan vendor:publish --provider="LukasMu\Glide\GlideServiceProvider" --tag="config"
```

It is highly recommended to add an additional symbolic link in your ```filesystems.php``` configuration file:

```php
'links' => [
    /// ...
    public_path('glide') => storage_path('framework/cache/glide'),
    // ...
],
```

The additional link ensures that cached images can be served directly to the users of your Laravel application.
Do not forget to run the ```php artisan storage:link``` command after adding the additional symbolic link (see [https://laravel.com/docs/filesystem#the-public-disk](https://laravel.com/docs/filesystem#the-public-disk)).

If you plan to modify the package views, you can publish them with:

```bash
php artisan vendor:publish --provider="LukasMu\Glide\GlideServiceProvider" --tag="views"
```

## Usage

### URL generator

You can generate image URLs using the `Glide` facade:

```php
Glide::getUrl('image.jpg', ['w' => 500, 'blur' => '5']);
```
This will generate a URL such as [http://localhost/glide/v2/aW1hZ2UuanBn/eyJibHVyIjoiNSIsInciOiI1MDAifQ.png?s=ac50711366fe50e5e03c6c0a312f3f75](http://localhost/glide/v2/aW1hZ2UuanBn/eyJibHVyIjoiNSIsInciOiI1MDAifQ.png?s=ac50711366fe50e5e03c6c0a312f3f75).
When hitting this URL, a 500px wide, slightly blurred version of `image.jpg` located in your ```public``` directory, will be automatically generated.

The first argument must be an image located in your `public` directory or in a remote location.
The second argument should contain the image manipulation parameters.
You can use any [Glide image manipulation options](https://glide.thephpleague.com/2.0/api/quick-reference/).

### View component

Furthermore, you can use the provided view component.
In your Blade templates, you can simply use:

```html
<x-glide::img src="image.jpg" data-glide-blur="5" alt="A test image" />
```

This will render the following `img` element and ensures a truly responsive experience for the users of your Laravel application.
No need to ever worry again about manually building responsive `img` elements!

```html
<img
    src="http://localhost/glide/v2/dGVzdC5qcGc/eyJibHVyIjoiNSJ9.jpg?s=b20f558c76fabc4491ab48e5bdd4bb4f"
    srcset="http://localhost/glide/v2/dGVzdC5qcGc/eyJibHVyIjoiNSIsInEiOiI4NSIsInciOiI0NjAifQ.webp?s=a0edb8786e9bec6e3f63a1a42545857b 460w, http://localhost/glide/v2/dGVzdC5qcGc/eyJibHVyIjoiNSIsInEiOiI4NSIsInciOiIzODUifQ.webp?s=2d28004e12d5b04666148f621a4b981d 385w, http://localhost/glide/v2/dGVzdC5qcGc/eyJibHVyIjoiNSIsInEiOiI4NSIsInciOiIzMjIifQ.webp?s=7b621534f7a064f1188bf98f2436c4ae 322w"
    loading="lazy"
    sizes="auto" 
    alt="A test image" 
>
```

Use the `data-glide-*` attributes to specify image manipulation parameters.
Use other attributes as you would normally do on `img` elements (e.g. the `alt` attribute).

### Clear cache command

Finally, you can remove all cached images by calling:

```bash
php artisan glide:clear
```

## Comparison to similar packages

### spatie/laravel-glide

The [`spatie/laravel-glide`](https://github.com/spatie/laravel-glide) package originally provided on-the-fly image manipulation but dropped this feature in version 3.

In contrast, `lukasmu/laravel-glide` fully supports dynamic image manipulation, which is particularly beneficial for responsive images where required dimensions vary.
Since images are generated on demand, there is no need to pre-create and store images of multiple sizes.
While this introduces a slight performance cost on the first request, an optimized caching mechanism ensures fast subsequent loads.

### ralphjsmit/laravel-glide

The [`ralphjsmit/laravel-glide`](https://github.com/ralphjsmit/laravel-glide) package focuses solely on responsive image generation.

`lukasmu/laravel-glide` offers a broader feature set, including all available Glide transformations.
Furthermore, it integrates better with/makes heavy use of advanced Laravel features, such as middleware (enhancing security) and view components (making the package straightforward to use).

## Technical details

### URL structure

The package encodes both the image path and its manipulation parameters using a slightly modified base64 encoding method.
These encoded values are then included directly in the URL path.
This differs from Glide's default behavior, where the image path remains unencoded and parameters are passed as query string values (e.g., `http://localhost/glide/v1/image.jpg?w=500&blur=5`).
Please note that this package also supports Glide's default behavior via redirects.

This approach offers two main advantages:
- Remote source compatibility: By encoding the image path, serving images from remote sources becomes straightforward.
- Efficient caching & proxy support: Since all necessary data is contained within the URL path itself, cached images can be served directly by a reverse proxy (e.g., NGINX) without requiring Laravel to handle routing or processing after the first request.

This method ensures optimal performance while maintaining the security and flexibility of on-the-fly image manipulations.

### URL signing

Generated URLs contain a signature derived from the `APP_KEY` environment variable.
This prevents unauthorized use of your Laravel application as a remote image manipulation service or as an image proxy.
URL validation is enforced via middleware.

### Image component

The `srcset` attribute in the Blade component is automatically generated. It:
- Requests images with 85% image quality.
- Requests images the modern WebP format.
- Requests images of different width.

The different widths are not static.
They rather depend on the original width and size of the image.
The larger the original image, the more versions are requested.
The motivation and mechanism has been adopted from [https://spatie.be/docs/laravel-medialibrary/v11/responsive-images/using-your-own-width-calculator](https://spatie.be/docs/laravel-medialibrary/v11/responsive-images/using-your-own-width-calculator).

The `loading` attribute is set to 'lazy' by default while the `sizes` attribute is set to 'auto'. 
This appears to be the most pragmatic approach. 
These values can be overridden to fine-tune the performance of your Laravel application.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are **welcome** and will be fully **credited**.
Feedback is very much appreciated as well.

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing

The package includes tests written for the [Pest](https://pestphp.com/) PHP testing framework which can be run by calling:

```bash
composer test
```

## Security

If you discover any security related issues, please email hello@lukasmu.com instead of using the issue tracker.

## Credits

- [Lukas Müller](https://github.com/lukasmu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT).
Please see [LICENSE](LICENSE.md) for more information.
