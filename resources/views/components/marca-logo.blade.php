{{--
    Logo de una marca en círculo. Si la marca no tiene logo (o el archivo no existe), muestra un monograma
    con sus iniciales en vez de un hueco o una imagen rota.
    Uso: <x-marca-logo :marca="$marca" :tam="56" />
    Por defecto el logo es decorativo (alt vacío) porque el nombre de la marca suele estar escrito al lado.
--}}
@props(['marca', 'tam' => 56, 'alt' => ''])
@php
    $existe = \App\Services\ImageVariants::existe($marca->foto_marca);
    $letras = preg_replace('/[^\p{L}\p{N}]/u', '', (string) $marca->nombre_marca);
    $iniciales = mb_strtoupper(mb_substr($letras !== '' ? $letras : '?', 0, 2));
@endphp
<span {{ $attributes->class(['marca-logo', $existe ? 'marca-logo--img' : 'marca-logo--tono' . ($marca->id % 3)]) }} style="--t: {{ $tam }}px">
    @if($existe)
        <img src="{{ \App\Services\ImageVariants::url($marca->foto_marca, 200) }}" alt="{{ $alt }}"
            width="{{ $tam }}" height="{{ $tam }}" loading="lazy" decoding="async">
    @else
        <span class="marca-logo-ini" aria-hidden="true">{{ $iniciales }}</span>
    @endif
</span>
