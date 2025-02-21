<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LukasMu\Glide\Facades\Glide;

use function Illuminate\Filesystem\join_paths;

it('handles non-existing image files gracefully', function () {
    $response = $this->get('glide/v2/invalid.jpg/W10.jpg?s=73d61fb2e8ee128ee4c59a749b0c694b');
    $response->assertStatus(404);
});

it('handles non-supported image formats gracefully', function () {
    $response = $this->get('glide/v2/test.jpg/W10.svg?s=c17affb7f8378878cba25c6a4e1219eb');
    $response->assertStatus(404);
});

function isProgressiveJpeg($data)
{
    for ($pos = 0, $len = strlen($data) - 1; $pos < $len; $pos++) {
        if ($data[$pos] === "\xFF") {
            $marker = ord($data[$pos + 1]);
            if ($marker === 0xC0) {
                return false;
            } // Baseline JPEG
            if ($marker === 0xC2) {
                return true;
            }  // Progressive JPEG
        }
    }

    return null; // No valid marker found
}

it('returns images in the requested format', function () {
    $response = $this->get('glide/v2/dGVzdC5qcGc/W10.jpg?s=c6698945a4008c3a4dea0ef416cd37e9');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
    $data = $response->streamedContent();
    $this->assertFalse(isProgressiveJpeg($data));

    $response = $this->get('glide/v2/dGVzdC5qcGc/W10.webp?s=34862a85276acc655f676056fa69666f');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/webp');

    $response = $this->get('glide/v2/dGVzdC5qcGc/eyJmbSI6InBqcGcifQ.jpg?s=a02766bb104094d8773bb50ff4f5cb7e');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
    $data = $response->streamedContent();
    $this->assertTrue(isProgressiveJpeg($data));
});

it('returns images that are contained in subdirectories', function () {
    $response = $this->get('glide/v2/c3ViZGlyL2ZpbGUucG5n/W10.png?s=2ccbd6f6837880578a50405edf6ee021');
    $response->assertStatus(200);
});

it('returns images in the requested size', function () {
    $response = $this->get('glide/v2/dGVzdC5qcGc/eyJ3IjoiMTIzIn0.jpg?s=b02d95dde4af1c1c4944af8ade843b2f');
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
    $data = $response->streamedContent();
    $info = getimagesizefromstring($data);
    $this->assertEquals(123, $info[0]);
});

it('returns images when a source path prefix is configured', function () {
    Config::set('glide.source_path_prefix', 'subdir');
    $path = 'glide/v2/ZmlsZS5wbmc/eyJ3IjoiMTExIn0.png?s=b01ac18735793790e1b5221642adb33d';

    $url = Glide::getUrl('file.png', ['w' => '111']);
    $this->assertEquals($url, asset($path));

    $response = $this->get($path);
    $response->assertStatus(200);
});

it('returns images that are stored in a remote location', function () {
    $url = Glide::getUrl('https://raw.githubusercontent.com/lukasmu/laravel-glide/refs/heads/main/public/test.jpg', ['fm' => 'webp']);
    $response = $this->get($url);
    $response->assertStatus(200);
});

it('caches images', function () {
    $path = join_paths(config('glide.cache'), 'v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg');
    $this->assertFalse(File::exists($path));
    $response = $this->get('glide/v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg?s=039fd9128dec1db308644a5561fefdc3');
    $response->assertStatus(200);
    $this->assertTrue(File::exists($path));
});

it('caches images and makes them publicly accessible', function () {

    Config::set('filesystems.links', [
        public_path('glide') => config('glide.cache'),
    ]);
    Artisan::call('storage:link');

    $this->assertFalse(File::exists(public_path('glide/v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg')));

    $response = $this->get('glide/v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg?s=039fd9128dec1db308644a5561fefdc3');
    $response->assertStatus(200);

    $this->assertTrue(File::exists(public_path('glide/v2/dGVzdC5qcGc/eyJyYW5kb20iOiJjYWNoZSIsInciOiIxMDAifQ.jpg')));
});
