{{-- Sección de productos de la portada, en fila que avanza sola. Recibe $titulo, $productos y opcionalmente $enlace y $textoEnlace. --}}
@if($productos->isNotEmpty())
    <section class="container-xl portada-section" aria-labelledby="sec-{{ \Illuminate\Support\Str::slug($titulo) }}">
        <div class="section-head">
            <h2 id="sec-{{ \Illuminate\Support\Str::slug($titulo) }}">{{ $titulo }}</h2>
            @isset($enlace)
                <a href="{{ $enlace }}">{{ $textoEnlace ?? 'Ver todo' }} <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>
            @endisset
        </div>
        <div class="fila-productos" data-fila>
            <div class="fila-pista {{ $productos->count() > 4 ? 'fila-pista--doble' : '' }}" data-pista>
                @foreach($productos as $p)
                    @include('partials.product-card-mini', ['p' => $p])
                @endforeach
            </div>
            @if($productos->count() > 2)
                <button type="button" class="fila-flecha fila-flecha--prev" data-dir="-1" aria-label="Anteriores"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
                <button type="button" class="fila-flecha fila-flecha--next" data-dir="1" aria-label="Siguientes"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
            @endif
        </div>
    </section>
@endif
