<?php

namespace NorthLab\Glide\Facades;

use Illuminate\Support\Facades\Facade;
use NorthLab\Glide\GlideService;

/**
 * @see \NorthLab\Glide\GlideService
 */
class Glide extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GlideService::class;
    }
}
