<?php

namespace NorthLab\Glide\Http\Controllers;

use Illuminate\Http\Request;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Server;
use NorthLab\Glide\Facades\Glide;

class GlideController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Server $server, string $encodedPath, string $encodedParams, string $extension): mixed
    {
        // Debug: Log the incoming parameters
        \Log::info('Glide Request Debug:', [
            'encoded_path' => $encodedPath,
            'encoded_params' => $encodedParams,
            'extension' => $extension,
            'query_params' => $request->query->all()
        ]);

        $path = Glide::decodePath($encodedPath);
        $params = Glide::decodeParams($encodedParams);
        $params['fm'] = $params['fm'] ?? $extension;

        // Debug: Log decoded values
        \Log::info('Glide Decoded Debug:', [
            'decoded_path' => $path,
            'decoded_params' => $params,
            'full_source_path' => config('glide.source') . '/' . $path,
            'file_exists' => file_exists(config('glide.source') . '/' . $path)
        ]);

        try {
            return $server->outputImage($path, $params);
        } catch (FileNotFoundException $e) {
            \Log::error('Glide File Not Found:', [
                'path' => $path,
                'full_path' => config('glide.source') . '/' . $path,
                'error' => $e->getMessage()
            ]);
            abort(404);
        } catch (\Exception $e) {
            \Log::error('Glide General Error:', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500);
        }
    }

    public function show(Request $request, string $path)
    {
        $server = app(Server::class);

        try {
            return $server->outputImage($path, $request->all());
        } catch (FileNotFoundException $e) {
            abort(404);
        }
    }

    public function redirect(Request $request, string $path)
    {
        return redirect()->to(Glide::getUrl($path, $request->all()));
    }
}
