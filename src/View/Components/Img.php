<?php

namespace LukasMu\Glide\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Intervention\Image\Facades\Image;
use LukasMu\Glide\Facades\Glide;

use function Illuminate\Filesystem\join_paths;

class Img extends Component
{
    public string $src;

    protected ?array $srcsetWidths;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $src,
        ?string $srcsetWidths = null,
    ) {
        $this->src = $src;  // TODO: Maybe allow src to be a full URL? Maybe even allow remote sources?
        $this->srcsetWidths = $srcsetWidths ? array_map('intval', explode(',', $srcsetWidths)) : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('glide::components.img');
    }

    /**
     * Get the attributes that are passed on to Glide.
     */
    protected function getGlideAttributes(): Collection
    {
        return collect($this->attributes->whereStartsWith('data-glide-'))
            ->mapWithKeys(fn ($item, string $key) => [Str::after($key, 'data-glide-') => $item]);
    }

    /**
     * Automatically calculate the widths that should be used for the srcset attribute.
     *
     * The logic has been taken from https://github.com/spatie/laravel-medialibrary/blob/main/src/ResponsiveImages/WidthCalculator/FileSizeOptimizedWidthCalculator.php.
     */
    protected function getSrcsetWidthsFromImg(): ?array
    {
        $image = rescue(fn () => Image::make(join_paths(config('glide.source'), $this->src)), null);

        if (is_null($image)) {
            return null;
        }

        $filesize = $image->filesize();
        $width = $image->width();
        $height = $image->height();

        $srcsetWidths = [$width];
        $ratio = $height / $width;
        $pixelPrice = $filesize / ($width * $height);

        while ($filesize *= 0.7) {
            $newWidth = (int) floor(sqrt(($filesize / $pixelPrice) / $ratio));
            $srcsetWidths[] = $newWidth;
            if ($newWidth < 20 || $filesize < 10240) {
                break;
            }
        }

        return $srcsetWidths;
    }

    /**
     * Get the widths that should be used for the srcset attribute.
     */
    protected function getSrcsetWidths(): ?array
    {
        if ($this->srcsetWidths) {
            return $this->srcsetWidths;
        }

        return $this->getSrcsetWidthsFromImg();
    }

    /**
     * Get the widths that should be used for the srcset attribute.
     */
    protected function getSrcsetWidthsCached(): ?array
    {
        return Cache::rememberForever(
            "glide::{$this->src}:srcset_widths",
            fn () => $this->getSrcsetWidths()
        );
    }

    public function src(): string
    {
        return Glide::getUrl($this->src, $this->getGlideAttributes()->toArray());
    }

    public function srcset(): ?string
    {
        $widths = $this->getSrcsetWidthsCached();

        if (is_null($widths)) {
            return null;
        }

        return collect($widths)->map(function (int $size) {
            $url = Glide::getUrl(
                $this->src,
                $this->getGlideAttributes()->merge(['q' => 85, 'fm' => 'webp', 'w' => $size])->toArray()
            );

            return "{$url} {$size}w";
        })->join(', ');
    }
}
