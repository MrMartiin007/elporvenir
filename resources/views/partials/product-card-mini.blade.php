{{-- Tarjeta compacta de producto para la portada. Requiere $p con marca y ultimaEntrada cargadas. --}}
<div class="col">
    <article class="card card-mini h-100">
        <a href="{{ $p->url }}" class="card-mini-img" tabindex="-1" aria-hidden="true">
            @if(!empty($p->oferta) && (int) $p->oferta > 0)
                <span class="badge-oferta">OFERTA</span>
            @endif
            <x-product-img :path="$p->foto_producto"
                :alt="$p->detalle_producto . ' - ' . ($p->marca->nombre_marca ?? 'El Porvenir')"
                sizes="(max-width: 768px) 50vw, 25vw" :width="400" :height="400"
                loading="lazy" decoding="async" />
        </a>
        <div class="card-body">
            <span class="mini-brand">{{ $p->marca->nombre_marca ?? 'El Porvenir' }}</span>
            <h3 class="mini-title"><a href="{{ $p->url }}">{{ $p->detalle_producto }}</a></h3>
            <span class="mini-price">Q. {{ number_format((float) $p->precio_actual, 2) }}</span>
            <form action="{{ route('cart.agregar') }}" method="POST" class="mt-auto pt-1">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $p->id }}">
                <input type="hidden" name="cantidad" value="1">
                <button type="submit" class="btn btn-theme w-100">
                    <i class="fas fa-cart-plus me-1"></i> Agregar
                </button>
            </form>
        </div>
    </article>
</div>
