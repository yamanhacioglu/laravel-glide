<?php

namespace LukasMu\Glide\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageServiceProvider;
use LukasMu\Glide\GlideServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            GlideServiceProvider::class,
            ImageServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app->usePublicPath(__DIR__.'/../public');
        $app['config']->set('glide.source', __DIR__.'/../public');
    }

    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            File::deleteDirectory(config('glide.cache'));
            File::delete(public_path('glide'));
            Artisan::call('cache:clear');
        });

        parent::setUp();
    }
}
