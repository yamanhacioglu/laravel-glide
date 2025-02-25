<?php

namespace LukasMu\Glide;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureInterface;
use League\Glide\Urls\UrlBuilder;
use League\Glide\Urls\UrlBuilderFactory;
use LukasMu\Glide\Console\Commands\GlideClearCommand;
use LukasMu\Glide\Facades\Glide;

class GlideServiceProvider extends ServiceProvider
{
    /**
     * Register the package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/glide.php', 'glide');

        $this->app->singleton(Glide::class, GlideService::class);

        $this->app->instance(SignatureInterface::class, SignatureFactory::create(config('app.key')));

        $this->app->bind(UrlBuilder::class, function (Application $app) {
            return UrlBuilderFactory::create(
                $app->make(UrlGenerator::class)->route('glide.redirect', ['path' => '/']),
                config('app.key')
            );
        });

        $this->app->bind(Server::class, function (Application $app) {
            return ServerFactory::create(config('glide'));
        });
    }

    /**
     * Bootstrap the package services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'glide');

        Blade::componentNamespace('LukasMu\\Glide\\View\\Components', 'glide');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/glide.php' => $this->app->configPath('glide.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/glide'),
            ], 'views');

            $this->commands([
                GlideClearCommand::class,
            ]);
        }
    }
}
