<?php

namespace LukasMu\Glide\Facades;

use Illuminate\Support\Facades\Facade;
use LukasMu\Glide\GlideService;

/**
 * @see \LukasMu\Glide\GlideService
 */
class Glide extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GlideService::class;
    }
}
