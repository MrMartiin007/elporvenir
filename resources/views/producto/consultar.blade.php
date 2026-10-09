<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Detalle del Producto') }}
        </h2>
    </x-slot>

    <div class="container py-1">
        <div class="bg-white rounded-4 shadow-sm p-5">
            <h2 class="mb-4 text-center fw-bold" style="color: #5c3d42;">Consultar Producto</h2>

            <div class="row g-3 mb-5 justify-content-center">
                <div class="col-md-8">
                    @include('partials.admin-buscador', [
                        'destino' => 'consultar',
                        'grande' => true,
                        'enfocar' => true,
                        'valor' => isset($productos) && $productos ? '' : request('buscar'),
                        'placeholder' => 'Escanear código de barras o escribir nombre…',
                    ])
                </div>
            </div>

            @if(isset($productos) && $productos->isNotEmpty())

                @foreach($productos as $producto)
                    <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header text-center py-3" style="background-color: #fae1e5;">
                            <h4 class="mb-0 fw-bold" style="color: #880e4f;">{{ $producto->detalle_producto }}</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <!-- Foto centrada -->
                                <div class="col-md-4 text-center mb-4 mb-md-0">
                                    <div class="p-2 d-inline-block bg-white rounded-circle shadow-sm">
                                        <img src="{{ asset('storage/' . $producto->foto_producto) }}" alt="Foto"
                                            class="img-fluid rounded-circle object-cover"
                                            style="width: 250px; height: 250px; object-fit: cover; border: 4px solid #fae1e5;">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="row gy-4">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small text-uppercase fw-bold">Código</label>
                                            <div class="p-3 rounded fw-bold fs-5"
                                                style="background-color: #fce4ec; color: #880e4f;">
                                                <i class="fas fa-barcode me-2"></i> {{ $producto->codigo_producto }}
                                            </div>
                                            @if($producto->codigos->isNotEmpty())
                                                <div class="small text-muted mt-2">
                                                    Otros códigos:
                                                    @foreach($producto->codigos as $extra)
                                                        <span class="badge bg-light text-dark border">{{ $extra->codigo }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted small text-uppercase fw-bold">Marca</label>
                                            <div class="p-3 rounded fw-bold fs-5"
                                                style="background-color: #fce4ec; color: #880e4f;">
                                                <i class="fas fa-tag me-2"></i>
                                                {{ $producto->marca->nombre_marca ?? 'Sin marca' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted small text-uppercase fw-bold">Stock
                                                Actual</label>
                                            <div class="p-3 rounded fw-bold fs-4"
                                                style="background-color: #e3f2fd; color: #0d47a1;">
                                                <i class="fas fa-boxes me-2"></i> {{ $producto->stock }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted small text-uppercase fw-bold">Precio
                                                Venta</label>
                                            <div class="p-3 rounded fw-bold fs-4"
                                                style="background-color: #d1e7dd; color: #0f5132;">
                                                <i class="fas fa-money-bill-wave me-2"></i>
                                                {{ $producto->ultimaEntrada?->precio_venta ? 'Q ' . number_format($producto->ultimaEntrada->precio_venta, 2) : '-' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted small text-uppercase fw-bold">Precio
                                                Costo</label>
                                            <div class="p-2 rounded text-muted">
                                                {{ $producto->ultimaEntrada?->precio_costo ? 'Q ' . number_format($producto->ultimaEntrada->precio_costo, 2) : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small text-uppercase fw-bold">Precio
                                                Docena</label>
                                            <div class="p-2 rounded text-muted">
                                                {{ $producto->ultimaEntrada?->precio_docena ? 'Q ' . number_format($producto->ultimaEntrada->precio_docena, 2) : '-' }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            @elseif(request('buscar'))
                <div class="alert text-center mt-4 shadow-sm border-0" style="background-color: #f8d7da; color: #842029;">
                    <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                    <h5 class="fw-bold">No encontrado</h5>
                    No se encontró ningún producto con ese código o nombre.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>