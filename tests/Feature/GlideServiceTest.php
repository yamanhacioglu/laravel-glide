<?php

use Illuminate\Support\Facades\App;
use League\Glide\Urls\UrlBuilder;
use LukasMu\Glide\Facades\Glide;

it('can build legacy URLs with parameters', function () {
    $url = App::make(UrlBuilder::class)->getUrl('image.jpg', ['w' => 300, 'h' => 200, 'fm' => 'png']);
    expect($url)->toEqual('http://localhost/glide/v1/image.jpg?w=300&h=200&fm=png&s=43f347dbe3a46da5acf740f8d91ecafb');
});

it('can build optimized URLs with parameters', function () {
    $url = Glide::getUrl('image.jpg', ['w' => 300, 'h' => 200, 'fm' => 'png']);
    expect($url)->toEqual('http://localhost/glide/v2/aW1hZ2UuanBn/eyJoIjoiMjAwIiwidyI6IjMwMCJ9.png?s=00cee02769a8dc3506989907dcd94758');
});

it('can build clean URLs without unnecessary parameters', function () {
    $url = Glide::getUrl('image.jpg', ['fm' => 'png', 's' => 'somethingirrelevant']);
    expect($url)->toEqual('http://localhost/glide/v2/aW1hZ2UuanBn/W10.png?s=4e3b29e3e6e0e27ac56ec1618c809491');
});

it('does not build URLs when the image can be served directly', function () {
    $url = Glide::getUrl('test.jpg');
    expect($url)->toEqual('http://localhost/test.jpg');

    $url = Glide::getUrl('https://raw.githubusercontent.com/lukasmu/laravel-glide/refs/heads/main/public/test.jpg');
    expect($url)->toEqual('https://raw.githubusercontent.com/lukasmu/laravel-glide/refs/heads/main/public/test.jpg');
});

it('makes sure that the optimized address and cache paths match', function () {
    $url = Glide::getUrl('image.jpg', ['w' => 300, 'h' => 200, 'fm' => 'png']);
    expect($url)->toEqual('http://localhost/glide/v2/aW1hZ2UuanBn/eyJoIjoiMjAwIiwidyI6IjMwMCJ9.png?s=00cee02769a8dc3506989907dcd94758');

    $path = Glide::getCachePath('image.jpg', ['w' => 300, 'h' => 200, 'fm' => 'png']);
    expect($path)->toEqual('/v2/aW1hZ2UuanBn/eyJoIjoiMjAwIiwidyI6IjMwMCJ9.png');

    expect(parse_url($url, PHP_URL_PATH))->toEqual('/'.config('glide.prefix').$path);
});
