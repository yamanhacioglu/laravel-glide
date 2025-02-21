<img 
    src="{{ $src() }}"
    srcset="{{ $srcset() }}"
    {{ $attributes->whereDoesntStartWith('data-glide-')->merge([
        'loading' => 'lazy', 
        'sizes' => 'auto', 
    ]) }}
>
