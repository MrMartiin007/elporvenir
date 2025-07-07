<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Detalle del Producto') }}
        </h2>
    </x-slot>

    <div class="container py-5">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <h2 class="mb-4">Consultar Producto</h2>

            <form method="GET" action="{{ route('productos.consultar') }}" class="row g-3 mb-5">
                <div class="col-md-6">
                    <input type="text" name="buscar"
                        value="{{ isset($productos) && $productos ? '' : request('buscar') }}" class="form-control"
                        placeholder="Escanear código de barras o escribir nombre..." autofocus>

                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>

            @if($productos)

                @foreach($productos as $producto)
                    <!-- Foto centrada -->
                    <div class="text-center mb-4">
                        <label class="form-label fw-bold text-muted">Foto del Producto</label><br>
                        <img src="{{ asset('storage/' . $producto->foto_producto) }}" alt="Foto"
                            class="img-fluid d-block mx-auto"
                            style="max-height: 200px; border-radius: 12px; border: 4px solid #fbc2eb; background: #fff;">

                    </div>


                    <div class="row gy-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Código:</label>
                            <div class="bg-light p-3 rounded fw-semibold">{{ $producto->codigo_producto }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted">Descripción:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->detalle_producto ?? 'No especificada' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted">Marca:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->marca->nombre_marca ?? 'Sin marca' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted">Stock:</label>
                            <div class="bg-light p-3 rounded fw-semibold">{{ $producto->stock }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Precio Costo:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->ultimaEntrada?->precio_costo ? 'Q ' . number_format($producto->ultimaEntrada->precio_costo, 2) : '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Precio Venta:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->ultimaEntrada?->precio_venta ? 'Q ' . number_format($producto->ultimaEntrada->precio_venta, 2) : '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Precio por Docena:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->ultimaEntrada?->precio_docena ? 'Q ' . number_format($producto->ultimaEntrada->precio_docena, 2) : '-' }}
                            </div>
                        </div>

                    </div>
                @endforeach

            @elseif(request('buscar'))
                <div class="alert alert-warning text-center mt-4">
                    No se encontró ningún producto con ese código o nombre.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>