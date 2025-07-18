<?php

namespace NorthLab\Glide;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Glide\Server;
use League\Glide\Signatures\SignatureInterface;
use Netzarbeiter\FlysystemHttp\HttpAdapterPsr;

use function Illuminate\Filesystem\join_paths;

class GlideService
{
    protected function getRouteParams(string $path, array $params): array
    {
        // Determine image format and corresponding extension
        $format = $params['fm'] ?? Str::before(pathinfo($path)['extension'], '?');
        $extension = $format === 'pjpg' ? 'jpg' : $format;

        // No need to include the format parameter if it coincides with the extension
        if ($format === $extension) {
            unset($params['fm']);
        }

        return [
            'encoded_path' => $this->encodePath($path),
            'encoded_params' => $this->encodeParams($params),
            'extension' => $extension,
        ];
    }

    public function getUrl(string $path, array $params = []): string
    {
        // The signature is created later and should be ignored even if provided as a parameter
        unset($params['s']);

        // Sometimes we can directly serve the image
        if (empty($params) && Str::isUrl($path)) {
            return $path;
        } elseif (empty($params) && Str::startsWith(config('glide.source'), public_path())) {
            return asset(join_paths(Str::after(config('glide.source'), public_path()), $path));
        }

        // Now we determine the route parameters including the signature (which depends on the other parameters)
        $routeParams = $this->getRouteParams($path, $params);
        $routeParams['s'] = App::make(SignatureInterface::class)->generateSignature(route('glide', $routeParams, false), []);

        return route('glide', $routeParams);
    }

    public function getCachePath(string $path, array $params = []): string
    {
        $routeParams = $this->getRouteParams($path, $params);

        return Str::after(route('glide', $routeParams, false), config('glide.prefix'));
    }

    public function getSourceFilesystem(string $path): Filesystem
    {
        $adapter = new LocalFilesystemAdapter(config('glide.source'));
        if (Str::isUrl($path)) {
            $adapter = HttpAdapterPsr::fromUrl('http://example.com');
        }

        return new Filesystem($adapter);
    }

    public function encodeParams(array $params): string
    {
        $params = App::make(Server::class)->getAllParams($params);
        unset($params['s'], $params['p']);
        $params = array_map('strval', $params);
        ksort($params);

        return rtrim(strtr(base64_encode(json_encode($params)), '+/', '-_'), '=');
    }

    public function decodeParams(string $str): array
    {
        return json_decode(base64_decode(strtr($str, '-_', '+/')), true);
    }

    public function encodePath(string $path): string
    {
        // We can shorten the path if it is an URL that points to a file in the public directory
        if (Str::isUrl($path) && Str::startsWith($path, config('app.url')) && ! Str::startsWith($path, url(config('glide.prefix')))) {
            $path = Str::after($path, config('app.url'));
        }

        // We can always get rid of leading slashes
        $path = ltrim($path, '/');

        return rtrim(strtr(base64_encode($path), '+/', '-_'), '=');
    }

    public function decodePath(string $str): string
    {
        return base64_decode(strtr($str, '-_', '+/'));
    }
}
