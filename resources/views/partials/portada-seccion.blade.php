{{-- Sección de productos de la portada. Recibe $titulo, $productos y opcionalmente $enlace y $textoEnlace. --}}
@if($productos->isNotEmpty())
    <section class="container-xl portada-section" aria-labelledby="sec-{{ \Illuminate\Support\Str::slug($titulo) }}">
        <div class="section-head">
            <h2 id="sec-{{ \Illuminate\Support\Str::slug($titulo) }}">{{ $titulo }}</h2>
            @isset($enlace)
                <a href="{{ $enlace }}">{{ $textoEnlace ?? 'Ver todo' }} <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>
            @endisset
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($productos as $p)
                @include('partials.product-card-mini', ['p' => $p])
            @endforeach
        </div>
    </section>
@endif
