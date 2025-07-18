<?php

namespace NorthLab\Glide\Http\Controllers;

use Illuminate\Http\Request;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use NorthLab\Glide\Facades\Glide;

class GlideController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Server $server, string $encodedPath, string $encodedParams, string $extension): mixed
    {
        $path = Glide::decodePath($encodedPath);

        $params = Glide::decodeParams($encodedParams);
        $params['fm'] = $params['fm'] ?? $extension;

        $server->setSource(Glide::getSourceFilesystem($path));
        $server->setCachePathCallable(fn (string $path, array $params = []): string => Glide::getCachePath($path, $params));
        $server->setResponseFactory(new SymfonyResponseFactory($request));

        try {
            return $server->getImageResponse($path, $params);
        } catch (FileNotFoundException) {
            abort(404);
        }
    }
}
