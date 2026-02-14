<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Venta ') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="mx-auto sm:px-6 lg:px-8">

            <div class="row">
                <!-- 🧾 Sección Principal -->
                <div class="col-md-9">
                    <div class="bg-white shadow-sm rounded p-4">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success m-4">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        @if ($message = Session::get('bad_status'))
                            <div class="alert alert-warning mb-4">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <form method="GET" action="{{ route('ventas.create') }}" id="formScan" class="flex-grow-1">
                                <input type="text" name="scan" id="scan" autofocus autocomplete="off"
                                    class="border border-gray-300 rounded form-control form-control-sm w-50"
                                    placeholder="Escanea un código...">
                            </form>
                            <form method="POST" action="{{ route('ventas.cerrar', $venta->id) }}"
                                onsubmit="return confirm('¿Estás seguro de cerrar esta venta?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-lock"></i> Cerrar Venta
                                </button>
                            </form>
                        </div>

                        <strong class="text-center align-middle"> Productos Vendidos</strong>
                        @if($venta->detalles->count() > 0)
                            <div class="table-responsive">
                                {{-- Desktop View (Table) --}}
                                <div class="d-none d-md-block">
                                    <table class="table table-hover table-bordered" id="tablaProductos">
                                        <thead class="text-center bg-light">
                                            <tr>
                                                <th class="d-none d-md-table-cell text-nowrap" style="width: 5%;">#</th>
                                                <th class="d-none d-lg-table-cell text-nowrap" style="width: 10%;">Código
                                                </th>
                                                <th style="min-width: 150px;">Producto</th>
                                                <th class="d-none d-sm-table-cell" style="width: 10%;">Foto</th>
                                                <th style="width: 10%;">Cant.</th>
                                                <th class="d-none d-sm-table-cell text-nowrap" style="width: 10%;">Precio
                                                </th>
                                                <th class="d-none d-md-table-cell text-nowrap" style="width: 10%;">Desc.
                                                </th>
                                                <th class="text-nowrap" style="width: 10%;">Subtotal</th>
                                                <th style="width: 10%;">Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($venta->detalles()->orderBy('created_at', 'desc')->get() as $detalle)
                                                <tr class="align-middle">
                                                    <td class="d-none d-md-table-cell text-center">{{ $loop->iteration }}</td>
                                                    <td class="d-none d-lg-table-cell text-center">
                                                        {{ $detalle->producto->codigo_producto }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span
                                                                class="fw-bold text-dark">{{ $detalle->producto->detalle_producto }}</span>
                                                            <small
                                                                class="d-lg-none text-muted">{{ $detalle->producto->codigo_producto }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center">
                                                        @if($detalle->producto->foto_producto)
                                                            <img src="{{ asset('storage/' . $detalle->producto->foto_producto) }}"
                                                                class="rounded-3 shadow-sm object-cover cursor-pointer"
                                                                style="width: 45px; height: 45px;" alt="Foto" data-bs-toggle="modal"
                                                                data-bs-target="#fotoModal"
                                                                onclick="mostrarImagen('{{ asset('storage/' . $detalle->producto->foto_producto) }}')">
                                                        @else
                                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted"
                                                                style="width: 45px; height: 45px;">
                                                                <i class="fas fa-image"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" name="cantidad"
                                                            value="{{ old('cantidad', $detalle->cantidad) }}" min="1"
                                                            form="f-actualizar-desktop-{{ $detalle->id }}"
                                                            class="text-center fw-bold text-center mx-auto"
                                                            style="max-width: 60px; border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 2px;"
                                                            onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
                                                    </td>
                                                    <td class="d-none d-sm-table-cell text-center text-nowrap">
                                                        @if($detalle->descuento > 0 && $detalle->descuento < $detalle->precio_unitario)
                                                            <div class="d-flex flex-column align-items-center" style="line-height: 1.2;">
                                                                <span class="text-decoration-line-through text-muted small">
                                                                    Q{{ number_format($detalle->precio_unitario, 2) }}
                                                                </span>
                                                                <span class="fw-bold text-dark">
                                                                    Q{{ number_format($detalle->precio_unitario - $detalle->descuento, 2) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            Q{{ number_format($detalle->precio_unitario, 2) }}
                                                        @endif
                                                    </td>
                                                    <td class="d-none d-md-table-cell text-center">
                                                        <input type="number" name="descuento"
                                                            value="{{ old('descuento', $detalle->descuento) }}" step="0.01"
                                                            min="0" max="{{ $detalle->precio_unitario }}"
                                                            form="f-actualizar-desktop-{{ $detalle->id }}"
                                                            class="text-center mx-auto"
                                                            style="max-width: 70px; border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 2px;"
                                                            placeholder="0.00" onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
                                                    </td>
                                                    <td class="fw-bold text-center text-nowrap text-primary">
                                                        Q
                                                        {{ number_format($detalle->cantidad * ($detalle->precio_unitario - ($detalle->descuento ?? 0)), 2) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                                            <button type="submit" form="f-actualizar-desktop-{{ $detalle->id }}"
                                                                class="btn btn-outline-success btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                                                                style="width: 32px; height: 32px;" title="Actualizar">
                                                                <i class="fas fa-check" style="font-size: 14px;"></i>
                                                            </button>
                                                            <form action="{{ route('ventas.destroy', $detalle->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('¿Eliminar este producto de la venta?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                                                                    style="width: 32px; height: 32px;" title="Eliminar">
                                                                    <i class="fas fa-trash-alt" style="font-size: 14px;"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Mobile View (Cards) --}}
                                <div class="d-md-none space-y-3">
                                    @foreach($venta->detalles()->orderBy('created_at', 'desc')->get() as $detalle)
                                        <div class="card shadow-sm border-0 mb-3 rounded-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-center gap-3">
                                                        {{-- Image --}}
                                                        @if($detalle->producto->foto_producto)
                                                            <img src="{{ asset('storage/' . $detalle->producto->foto_producto) }}"
                                                                class="rounded-3 shadow-sm object-cover"
                                                                style="width: 60px; height: 60px;" alt="Foto" data-bs-toggle="modal"
                                                                data-bs-target="#fotoModal"
                                                                onclick="mostrarImagen('{{ asset('storage/' . $detalle->producto->foto_producto) }}')">
                                                        @else
                                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted"
                                                                style="width: 60px; height: 60px;">
                                                                <i class="fas fa-camera fa-lg"></i>
                                                            </div>
                                                        @endif

                                                        {{-- Product Info --}}
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0 text-break"
                                                                style="max-width: 180px;">
                                                                {{ $detalle->producto->detalle_producto }}
                                                            </h6>
                                                            <small class="text-muted d-block">{{ $detalle->producto->codigo_producto }}</small>
                                                            
                                                            {{-- Unit Price Breakdown for Mobile --}}
                                                            <div class="small text-muted mt-1">
                                                                @if($detalle->descuento > 0)
                                                                     <span class="text-decoration-line-through me-1">Q{{ number_format($detalle->precio_unitario, 2) }}</span>
                                                                     <span class="fw-bold text-dark">Q{{ number_format($detalle->precio_unitario - $detalle->descuento, 2) }} c/u</span>
                                                                @else
                                                                    <span>Q{{ number_format($detalle->precio_unitario, 2) }} c/u</span>
                                                                @endif
                                                            </div>

                                                            <div class="text-primary fw-bold mt-1">
                                                                Q
                                                                {{ number_format($detalle->cantidad * ($detalle->precio_unitario - ($detalle->descuento ?? 0)), 2) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Delete Button (Top Right) --}}
                                                    <form action="{{ route('ventas.destroy', $detalle->id) }}" method="POST"
                                                        onsubmit="return confirm('¿Eliminar este producto?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                                                            style="width: 32px; height: 32px;">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- Controls Row --}}
                                                <div class="d-flex align-items-center gap-2 mt-3 bg-light p-2 rounded-3">
                                                    <div class="flex-grow-1">
                                                        <label class="small text-muted mb-1 d-block">Cantidad</label>
                                                        <input type="number" name="cantidad"
                                                            value="{{ old('cantidad', $detalle->cantidad) }}" min="1"
                                                            form="f-actualizar-mobile-{{ $detalle->id }}"
                                                            class="text-center fw-bold w-100"
                                                            style="border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 5px;"
                                                            onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <label class="small text-muted mb-1 d-block">Desc.</label>
                                                        <input type="number" name="descuento"
                                                            value="{{ old('descuento', $detalle->descuento) }}" step="0.01"
                                                            min="0" max="{{ $detalle->precio_unitario }}"
                                                            form="f-actualizar-mobile-{{ $detalle->id }}"
                                                            class="text-center w-100"
                                                            style="border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 5px;"
                                                            placeholder="0.00" onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
                                                    </div>

                                                    <div class="align-self-end pb-1">
                                                        <button type="submit" form="f-actualizar-mobile-{{ $detalle->id }}"
                                                            class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                            style="width: 36px; height: 36px;" title="Guardar Cambios">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Formularios fuera del tbody para evitar conflicto con DataTable --}}
                                @foreach($venta->detalles as $detalle)
                                    {{-- Formulario para Desktop --}}
                                    <form id="f-actualizar-desktop-{{ $detalle->id }}"
                                        action="{{ route('ventas.update', $detalle->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                    </form>

                                    {{-- Formulario para Mobile --}}
                                    <form id="f-actualizar-mobile-{{ $detalle->id }}"
                                        action="{{ route('ventas.update', $detalle->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </div>
                </div>
                <!-- 📋 Resumen lateral -->
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-header  text-dark">
                            <strong>Resumen de la Venta</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>
                            <p><strong>Productos:</strong> {{ $venta->cantidad_productos }}</p>
                            <p><strong>Total:</strong>
                                <span class="text-danger fw-bold">
                                    Q{{ number_format($venta->total_vendido, 2) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Card de Códigos No Encontrados -->
                    <div class="card mb-3">
                        <div class="card-header text-dark">
                            <strong>⚠️ Códigos No Encontrados</strong>
                            @if($venta->codigosNoEncontrados->count() > 0)
                                <span class="badge bg-danger">{{ $venta->codigosNoEncontrados->count() }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($venta->codigosNoEncontrados->count() > 0)
                                <div class="list-group">
                                    @foreach($venta->codigosNoEncontrados as $codigo)
                                        <div class="list-group-item p-3 border-start border-4 border-warning">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1 me-3">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-dark me-2">
                                                            <i class="fas fa-barcode"></i>
                                                        </span>
                                                        <strong class="text-dark">{{ $codigo->codigo }}</strong>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    {{-- Botón para buscar en Google --}}
                                                    <a href="https://www.google.com/search?q={{ urlencode($codigo->codigo) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary d-flex align-items-center"
                                                        title="Buscar en Google">
                                                        <i class="fab fa-google me-1"></i>
                                                        <span class="d-none d-md-inline"></span>
                                                    </a>

                                                    {{-- Botón para crear producto con código prellenado --}}
                                                    <a href="{{ route('productos.create', ['codigo' => $codigo->codigo]) }}"
                                                        target="_blank" class="btn btn-sm btn-success d-flex align-items-center"
                                                        title="Crear Producto">
                                                        <i class="fas fa-plus-circle me-1"></i>
                                                        <span class="d-none d-md-inline"></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">
                                    <small>No hay códigos sin registrar</small>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- End Row -->
        </div>
    </div>


    <!-- Modal para mostrar imagen -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Foto del producto" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        let table = $('#tablaProductos').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            language: {
                "emptyTable": "No hay productos registrados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ productos",
                "infoEmpty": "Mostrando 0 a 0 de 0 productos",
                "infoFiltered": "(filtrado de _MAX_ productos)",
                "lengthMenu": "Mostrar _MENU_ productos",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron coincidencias",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });

        table.columns.adjust();

        $(window).resize(function () {
            table.columns.adjust();
        });
    });

    function mostrarImagen(ruta) {
        document.getElementById('modalImage').src = ruta;
    }


</script>