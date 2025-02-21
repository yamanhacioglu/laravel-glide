<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use LukasMu\Glide\Facades\Glide;
use LukasMu\Glide\Http\Controllers\GlideController;
use LukasMu\Glide\Http\Middleware\VerifyGlideSignature;

Route::prefix(config('glide.prefix'))->middleware(VerifyGlideSignature::class)->group(function () {
    // Legacy route: Image manipulation parameters are provided as URL parameters
    Route::get('v1/{path}', fn (Request $request, string $path) => Redirect::to(Glide::getUrl($path, $request->all())))
        ->where('path', '.*')
        ->name('glide.redirect');

    // Optimized route: Image manipulation parameters are provided as part of the URL path
    Route::get('v2/{encoded_path}/{encoded_params}.{extension}', GlideController::class)
        ->whereIn('extension', ['jpg', 'png', 'gif', 'webp', 'avif', 'tiff'])
        ->name('glide');
});
