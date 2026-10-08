{{--
    Imagen de producto optimizada: sirve WebP responsive si existe y cae al original si no.
    Uso: <x-product-img :path="$producto->foto_producto" alt="..." sizes="..." :width="400" :height="400" />
    Cualquier otro atributo (class, loading, fetchpriority, onload...) pasa directo al <img>.
    Si el producto no tiene foto registrada (ruta vacía) no dibuja nada.
--}}
@props([
    'path',
    'alt' => '',
    'sizes' => '(max-width: 768px) 50vw, 25vw',
    'width' => null,
    'height' => null,
    'widths' => \App\Services\ImageVariants::WIDTHS,
])
@if(filled($path))
    @php $srcset = \App\Services\ImageVariants::srcset($path, $widths); @endphp
    <picture>
        @if($srcset)
            <source type="image/webp" srcset="{{ $srcset }}" sizes="{{ $sizes }}">
        @endif
        <img src="{{ asset('storage/' . $path) }}" alt="{{ $alt }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            {{ $attributes }}>
    </picture>
@endif
