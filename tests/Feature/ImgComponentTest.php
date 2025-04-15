<?php

use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;

it('renders the default img component correctly', function () {
    $html = (string) $this->blade('<x-glide::img src="test.jpg" data-glide-con="33" class="w-50" />');

    $crawler = new Crawler($html);
    $img = $crawler->filterXPath('descendant-or-self::img');
    expect($img->attr('src'))->toBe('http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyJ9.jpg?s=f875ac9f86d6b4a25b914dff92b0959a');
    expect($img->attr('srcset'))->toBe('http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiIxOTIwIn0.webp?s=5968d75d29c37a73f85956f874b28f23 1920w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiIxNjA2In0.webp?s=3c51afa56cb316f6df3dc92207f1c96c 1606w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiIxMzQ0In0.webp?s=74e4cd5a776242fd53e3ef3a35729af2 1344w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiIxMTI0In0.webp?s=1be1baef27f726fcbb1e5e19ea82e7df 1124w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiI5NDAifQ.webp?s=df5deadcb8a0b5ea8241ad73f4b8e5cf 940w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiI3ODcifQ.webp?s=86de2001a70a718c5e11e9423826f652 787w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiI2NTgifQ.webp?s=db205c775a1715bacec645ed02cb22f3 658w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiI1NTAifQ.webp?s=2e60c9160b3ce5a130c9597910bc09d9 550w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiI0NjAifQ.webp?s=598d50a35c17d76fdf8f3fec25721e86 460w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiIzODUifQ.webp?s=a60f8f69403a3e3a0fee3c5ba68bd9aa 385w, http://localhost/glide/v2/dGVzdC5qcGc/eyJjb24iOiIzMyIsInEiOiI4NSIsInciOiIzMjIifQ.webp?s=42486a0eff35cab124b94d71f029d048 322w');
    expect($img->attr('loading'))->toBe('lazy');
    expect($img->attr('sizes'))->toBe('auto');
    expect($img->attr('class'))->toBe('w-50');

    expect($html)->not->toContain('data-glide');
});

it('renders a custom img component with plain src correctly', function () {
    $html = (string) $this->blade('<x-glide::img src="test.jpg" loading="eager" sizes="100vw" class="w-100" />');

    $crawler = new Crawler($html);
    $img = $crawler->filterXPath('descendant-or-self::img');
    expect($img->attr('src'))->toBe('http://localhost/test.jpg');
    expect($img->attr('srcset'))->not->toBeEmpty();
    expect($img->attr('loading'))->toBe('eager');
    expect($img->attr('sizes'))->toBe('100vw');
    expect($img->attr('class'))->toBe('w-100');
});

it('renders a custom img component with invalid src correctly', function () {
    $html = (string) $this->blade('<x-glide::img src="invalid.jpg" />');

    $crawler = new Crawler($html);
    $img = $crawler->filterXPath('descendant-or-self::img');
    expect($img->attr('src'))->toBe('http://localhost/invalid.jpg');
    expect($img->attr('srcset'))->toBeEmpty();
});

it('caches srcset widths', function () {
    $src = 'test.jpg';

    expect(Cache::get("glide::{$src}:srcset_widths"))->toBeNull();

    $this->blade("<x-glide::img src='{$src}' loading='eager' sizes='100vw' class='w-100' />");

    expect(Cache::get("glide::{$src}:srcset_widths"))->toEqual([
        1920, 1606, 1344, 1124, 940, 787, 658, 550, 460, 385, 322,
    ]);
});

it('accepts custom srcset widths', function () {
    $src = 'test.jpg';

    expect(Cache::get("glide::{$src}:srcset_widths"))->toBeNull();

    $this->blade("<x-glide::img src='{$src}' srcset_widths='100,200,500' loading='eager' sizes='100vw' class='w-100' />");

    expect(Cache::get("glide::{$src}:srcset_widths"))->toEqual([
        100, 200, 500,
    ]);
});
