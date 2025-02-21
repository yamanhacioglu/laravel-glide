<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

use function Illuminate\Filesystem\join_paths;

it('works as expected', function () {
    $path = join_paths(config('glide.cache'), 'v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg');

    $this->assertFalse(File::exists($path));

    $response = $this->get('glide/v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg?s=039fd9128dec1db308644a5561fefdc3');
    $response->assertStatus(200);

    $this->assertTrue(File::exists($path));

    Artisan::call('glide:clear');

    $this->assertFalse(File::exists($path));
});
