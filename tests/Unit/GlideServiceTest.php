<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use LukasMu\Glide\Facades\Glide;
use Netzarbeiter\FlysystemHttp\HttpAdapterPsr;

it('can encode parameters', function () {
    expect(Glide::encodeParams([]))->toEqual('W10');
    expect(Glide::encodeParams(['w' => 123]))->toEqual('eyJ3IjoiMTIzIn0');
});

it('can decode parameters', function () {
    expect(Glide::decodeParams('W10'))->toEqual([]);
    expect(Glide::decodeParams('eyJ3IjoiMTIzIn0'))->toEqual(['w' => 123]);
});

it('treats all parameters as strings while encoding them', function () {
    $encodedParamsA = Glide::encodeParams(['w' => 200]);
    $encodedParamsB = Glide::encodeParams(['w' => '200']);
    expect($encodedParamsA === $encodedParamsB)->toEqual(true);
});

it('ignores the order of parameters while encoding them', function () {
    $encodedParamsA = Glide::encodeParams(['w' => 200, 'h' => 100]);
    $encodedParamsB = Glide::encodeParams(['h' => 100, 'w' => 200]);
    expect($encodedParamsA)->toEqual($encodedParamsB);
});

it('ignores the signature and preset parameters while encoding parameters', function () {
    $encodedParams = Glide::encodeParams(['w' => 200, 's' => 'abc', 'p' => 'xyz']);
    $decodedParams = Glide::decodeParams($encodedParams);
    expect($decodedParams)->toEqual(['w' => 200]);
});

it('can encode paths', function () {
    expect(Glide::encodePath('image.jpg'))->toEqual('aW1hZ2UuanBn');
    expect(Glide::encodePath('/image.jpg'))->toEqual('aW1hZ2UuanBn');
    expect(Glide::encodePath('http://localhost/image.jpg'))->toEqual('aW1hZ2UuanBn');
    expect(Glide::encodePath('http://localhost/glide/v1/image.jpg'))->toEqual('aHR0cDovL2xvY2FsaG9zdC9nbGlkZS92MS9pbWFnZS5qcGc');
    expect(Glide::encodePath('subdir/file.png'))->toEqual('c3ViZGlyL2ZpbGUucG5n');
    expect(Glide::encodePath('http://example.org/image.jpg'))->toEqual('aHR0cDovL2V4YW1wbGUub3JnL2ltYWdlLmpwZw');
    expect(Glide::encodePath('http://example.org/image.jpg?something=1'))->toEqual('aHR0cDovL2V4YW1wbGUub3JnL2ltYWdlLmpwZz9zb21ldGhpbmc9MQ');
});

it('can decode paths', function () {
    expect(Glide::decodePath('aW1hZ2UuanBn'))->toEqual('image.jpg');
    expect(Glide::decodePath('aHR0cDovL2xvY2FsaG9zdC9nbGlkZS92MS9pbWFnZS5qcGc'))->toEqual('http://localhost/glide/v1/image.jpg');
    expect(Glide::decodePath('c3ViZGlyL2ZpbGUucG5n'))->toEqual('subdir/file.png');
    expect(Glide::decodePath('aHR0cDovL2V4YW1wbGUub3JnL2ltYWdlLmpwZw'))->toEqual('http://example.org/image.jpg');
    expect(Glide::decodePath('aHR0cDovL2V4YW1wbGUub3JnL2ltYWdlLmpwZz9zb21ldGhpbmc9MQ'))->toEqual('http://example.org/image.jpg?something=1');
});

it('can select the correct source filesystem dapter', function () {
    $reflection = new \ReflectionClass(Filesystem::class);
    $adapterProperty = $reflection->getProperty('adapter');
    $adapterProperty->setAccessible(true);

    $filesystem = Glide::getSourceFilesystem('image.jpg');
    expect($adapterProperty->getValue($filesystem))->toBeInstanceOf(LocalFilesystemAdapter::class);

    $filesystem = Glide::getSourceFilesystem('http://example.org/image.jpg');
    expect($adapterProperty->getValue($filesystem))->toBeInstanceOf(HttpAdapterPsr::class);
});
