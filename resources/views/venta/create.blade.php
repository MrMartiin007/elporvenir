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
                        {{-- Toast de feedback AJAX (oculto por defecto) --}}
                        <div id="toast-scan" class="alert mb-3 d-none" role="alert"></div>

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
                            <form id="formScan" class="flex-grow-1" autocomplete="off">
                                @csrf
                                <div class="position-relative d-inline-block" style="width: 50%;">
                                    <input type="text" name="scan" id="scan" autofocus autocomplete="off"
                                        class="border border-gray-300 rounded form-control form-control-sm"
                                        placeholder="Escanea un código...">
                                    <span id="scan-spinner"
                                        class="position-absolute top-50 end-0 translate-middle-y me-2 d-none">
                                        <span class="spinner-border spinner-border-sm text-secondary" role="status"></span>
                                    </span>
                                </div>
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
                                                <th>ID</th>
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
                                            @foreach($venta->detalles()->with('producto')->orderBy('created_at', 'desc')->get() as $detalle)
                                                <tr class="align-middle">
                                                    <td>{{ $detalle->id }}</td>
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
                                                            class="text-center fw-bold text-center mx-auto"
                                                            style="max-width: 60px; border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 2px;"
                                                            onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                                                            onchange="actualizarDetalle(this, {{ $detalle->id }})"
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
                                                            class="text-center mx-auto"
                                                            style="max-width: 70px; border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 2px;"
                                                            placeholder="0.00" 
                                                            onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                                                            onchange="actualizarDetalle(this, {{ $detalle->id }})"
                                                            onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
                                                    </td>
                                                    <td class="fw-bold text-center text-nowrap text-primary subtotal-text" id="subtotal-desktop-{{ $detalle->id }}">
                                                        Q{{ number_format($detalle->cantidad * ($detalle->precio_unitario - ($detalle->descuento ?? 0)), 2) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-2">
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
                                    @foreach($venta->detalles()->with('producto')->orderBy('created_at', 'desc')->get() as $detalle)
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

                                                            <div class="text-primary fw-bold mt-1 subtotal-text" id="subtotal-mobile-{{ $detalle->id }}">
                                                                Q{{ number_format($detalle->cantidad * ($detalle->precio_unitario - ($detalle->descuento ?? 0)), 2) }}
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
                                                            class="text-center fw-bold w-100"
                                                            style="border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 5px;"
                                                            onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                                                            onchange="actualizarDetalle(this, {{ $detalle->id }})"
                                                            onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <label class="small text-muted mb-1 d-block">Desc.</label>
                                                        <input type="number" name="descuento"
                                                            value="{{ old('descuento', $detalle->descuento) }}" step="0.01"
                                                            min="0" max="{{ $detalle->precio_unitario }}"
                                                            class="text-center w-100"
                                                            style="border: none; border-bottom: 2px solid #ced4da; background: transparent; outline: none; padding: 5px;"
                                                            placeholder="0.00" 
                                                            onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                                                            onchange="actualizarDetalle(this, {{ $detalle->id }})"
                                                            onfocus="this.style.borderBottomColor='#0d6efd'"
                                                            onblur="this.style.borderBottomColor='#ced4da'">
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
                        <div class="card-header text-dark d-flex justify-content-between align-items-center">
                            <strong>Resumen de la Venta</strong>
                            @role('superadmin')
                                @if(isset($ventasActivasOtrosUsuarios) && $ventasActivasOtrosUsuarios->count() > 0)
                                    <div class="d-flex gap-1" title="Usuarios con ventas activas">
                                        @foreach($ventasActivasOtrosUsuarios as $vActiva)
                                            <a href="{{ route('ventas.show', $vActiva->id) }}" target="_blank"
                                                class="rounded-circle bg-primary opacity-75 shadow-sm" 
                                                style="width: 10px; height: 10px; display: inline-block; transition: all 0.2s;"
                                                onmouseover="this.style.transform='scale(1.3)'; this.style.opacity='1';"
                                                onmouseout="this.style.transform='scale(1)'; this.style.opacity='0.75';"
                                                title="Revisar carrito de {{ $vActiva->user->name }}"></a>
                                        @endforeach
                                    </div>
                                @endif
                            @endrole
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>
                            <p><strong>Productos:</strong> <span id="resumen-cantidad">{{ $venta->cantidad_productos }}</span></p>
                            <p><strong>Total:</strong>
                                <span class="text-danger fw-bold" id="resumen-total">
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
                                <span class="badge bg-danger" id="badge-codigos">{{ $venta->codigosNoEncontrados->count() }}</span>
                            @else
                                <span class="badge bg-danger d-none" id="badge-codigos">0</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($venta->codigosNoEncontrados->count() > 0)
                                <div class="list-group" id="lista-codigos-no-encontrados">
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
                                <div class="list-group" id="lista-codigos-no-encontrados" style="display:none;"></div>
                                <p class="text-muted text-center mb-0" id="codigos-no-encontrados-empty">
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
    // ─── Inicialización de DataTable ────────────────────────────────────────────
    let dtTable;
    $(document).ready(function () {
        dtTable = $('#tablaProductos').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            order: [[0, 'desc']], // Ordenar por la columna ID (oculta) de forma descendente
            columnDefs: [
                { targets: 0, visible: false, searchable: false } // Ocultar la columna ID
            ],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            language: {
                "emptyTable":   "No hay productos registrados",
                "info":         "Mostrando _START_ a _END_ de _TOTAL_ productos",
                "infoEmpty":    "Mostrando 0 a 0 de 0 productos",
                "infoFiltered": "(filtrado de _MAX_ productos)",
                "lengthMenu":   "Mostrar _MENU_ productos",
                "search":       "Buscar:",
                "zeroRecords":  "No se encontraron coincidencias",
                "paginate": {
                    "first": "Primero", "last": "Último",
                    "next": "Siguiente", "previous": "Anterior"
                }
            }
        });

        // Recalcular los números de fila (#) automáticamente al ordenar o buscar
        dtTable.on('order.dt search.dt', function () {
            let i = 1;
            dtTable.cells(null, 1, { search: 'applied', order: 'applied' }).every(function () {
                this.data(i++);
            });
        });

        dtTable.columns.adjust();
        $(window).resize(() => dtTable.columns.adjust());
    });

    // ─── Helpers ────────────────────────────────────────────────────────────────
    function mostrarImagen(ruta) {
        document.getElementById('modalImage').src = ruta;
    }

    function mostrarToast(mensaje, tipo) {
        // tipo: 'success' | 'warning' | 'danger'
        const toast = document.getElementById('toast-scan');
        toast.className = `alert alert-${tipo} mb-3`;
        toast.innerHTML = mensaje;
        toast.classList.remove('d-none');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.add('d-none'), 3500);
    }

    // ─── Formato de número ───────────────────────────────────────────────────────
    function fmt(num) {
        return parseFloat(num).toFixed(2);
    }

    // ─── Actualización AJAX de Cantidad / Descuento ─────────────────────────────
    function actualizarDetalle(input, id) {
        const fieldName = input.name; // 'cantidad' o 'descuento'
        const value = input.value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        input.disabled = true;

        fetch(`/admin/ventas/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                _method: 'PATCH',
                [fieldName]: value
            })
        })
        .then(response => response.json())
        .then(data => {
            input.disabled = false;
            if (data.status === 'success') {
                const subDesktop = document.getElementById(`subtotal-desktop-${id}`);
                const subMobile = document.getElementById(`subtotal-mobile-${id}`);
                if (subDesktop) subDesktop.textContent = `Q${data.subtotal}`;
                if (subMobile) subMobile.textContent = `Q${data.subtotal}`;
                
                document.querySelectorAll(`input[name="${fieldName}"]`).forEach(el => {
                    if (el !== input && el.getAttribute('onchange')?.includes(id)) {
                        el.value = value;
                    }
                });

                document.getElementById('resumen-cantidad').textContent = data.resumen.cantidad_productos;
                document.getElementById('resumen-total').textContent = `Q${data.resumen.total_vendido}`;
                
                input.style.transition = 'background-color 0.4s';
                input.style.backgroundColor = '#d1e7dd';
                setTimeout(() => { input.style.backgroundColor = 'transparent'; }, 800);
            } else {
                mostrarToast('<i class="fas fa-exclamation-triangle me-1"></i> Error al actualizar', 'warning');
            }
        })
        .catch(err => {
            input.disabled = false;
            mostrarToast('<i class="fas fa-times-circle me-1"></i> Error de conexión', 'danger');
        });
    }

    // ─── Construir fila para la tabla DataTable ──────────────────────────────────
    function buildFila(d, iteracion) {
        const fotoHtml = d.foto
            ? `<img src="${d.foto}" class="rounded-3 shadow-sm object-cover cursor-pointer"
                   style="width:45px;height:45px;" alt="Foto"
                   data-bs-toggle="modal" data-bs-target="#fotoModal"
                   onclick="mostrarImagen('${d.foto}')">`
            : `<div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted"
                    style="width:45px;height:45px;"><i class="fas fa-image"></i></div>`;

        const rutaActualizar = `/admin/ventas/${d.id}`;
        const rutaEliminar   = `/admin/ventas/${d.id}`;
        const csrfToken      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        return `<tr class="align-middle fila-nueva" id="fila-detalle-${d.id}">
            <td>${d.id}</td>
            <td class="d-none d-md-table-cell text-center"></td>
            <td class="d-none d-lg-table-cell text-center">${d.codigo}</td>
            <td>
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark">${d.nombre}</span>
                    <small class="d-lg-none text-muted">${d.codigo}</small>
                </div>
            </td>
            <td class="d-none d-sm-table-cell text-center">${fotoHtml}</td>
            <td class="text-center">
                <input type="number" name="cantidad" value="${d.cantidad}" min="1"
                    class="text-center fw-bold mx-auto"
                    style="max-width:60px;border:none;border-bottom:2px solid #ced4da;background:transparent;outline:none;padding:2px;"
                    onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                    onchange="actualizarDetalle(this, ${d.id})"
                    onfocus="this.style.borderBottomColor='#0d6efd'"
                    onblur="this.style.borderBottomColor='#ced4da'">
            </td>
            <td class="d-none d-sm-table-cell text-center text-nowrap">Q${fmt(d.precio_unitario)}</td>
            <td class="d-none d-md-table-cell text-center">
                <input type="number" name="descuento" value="${fmt(d.descuento)}" step="0.01" min="0" max="${fmt(d.precio_unitario)}"
                    class="text-center mx-auto"
                    style="max-width:70px;border:none;border-bottom:2px solid #ced4da;background:transparent;outline:none;padding:2px;"
                    placeholder="0.00"
                    onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                    onchange="actualizarDetalle(this, ${d.id})"
                    onfocus="this.style.borderBottomColor='#0d6efd'"
                    onblur="this.style.borderBottomColor='#ced4da'">
            </td>
            <td class="fw-bold text-center text-nowrap text-primary subtotal-text" id="subtotal-desktop-${d.id}">Q${fmt(d.subtotal)}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <form action="${rutaEliminar}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar este producto de la venta?')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                            class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                            style="width:32px;height:32px;" title="Eliminar">
                            <i class="fas fa-trash-alt" style="font-size:14px;"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>`;
    }

    // ─── Construir card móvil ────────────────────────────────────────────────────
    function buildCard(d) {
        const fotoHtml = d.foto
            ? `<img src="${d.foto}" class="rounded-3 shadow-sm object-cover"
                   style="width:60px;height:60px;" alt="Foto"
                   data-bs-toggle="modal" data-bs-target="#fotoModal"
                   onclick="mostrarImagen('${d.foto}')">`
            : `<div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted"
                    style="width:60px;height:60px;"><i class="fas fa-camera fa-lg"></i></div>`;

        const rutaEliminar = `/admin/ventas/${d.id}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        return `<div class="card shadow-sm border-0 mb-3 rounded-3 fila-nueva" id="card-detalle-${d.id}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        ${fotoHtml}
                        <div>
                            <h6 class="fw-bold text-dark mb-0 text-break" style="max-width:180px;">${d.nombre}</h6>
                            <small class="text-muted d-block">${d.codigo}</small>
                            <div class="small text-muted mt-1">
                                <span>Q${fmt(d.precio_unitario)} c/u</span>
                            </div>
                            <div class="text-primary fw-bold mt-1 subtotal-text" id="subtotal-mobile-${d.id}">Q${fmt(d.subtotal)}</div>
                        </div>
                    </div>
                    <form action="${rutaEliminar}" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                            class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                            style="width:32px;height:32px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                <div class="d-flex align-items-center gap-2 mt-3 bg-light p-2 rounded-3">
                    <div class="flex-grow-1">
                        <label class="small text-muted mb-1 d-block">Cantidad</label>
                        <input type="number" name="cantidad" value="${d.cantidad}" min="1"
                            class="text-center fw-bold w-100"
                            style="border:none;border-bottom:2px solid #ced4da;background:transparent;outline:none;padding:5px;"
                            onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                            onchange="actualizarDetalle(this, ${d.id})"
                            onfocus="this.style.borderBottomColor='#0d6efd'"
                            onblur="this.style.borderBottomColor='#ced4da'">
                    </div>
                    <div class="flex-grow-1">
                        <label class="small text-muted mb-1 d-block">Desc.</label>
                        <input type="number" name="descuento" value="${fmt(d.descuento)}" step="0.01" min="0" max="${fmt(d.precio_unitario)}"
                            class="text-center w-100"
                            style="border:none;border-bottom:2px solid #ced4da;background:transparent;outline:none;padding:5px;"
                            placeholder="0.00"
                            onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}"
                            onchange="actualizarDetalle(this, ${d.id})"
                            onfocus="this.style.borderBottomColor='#0d6efd'"
                            onblur="this.style.borderBottomColor='#ced4da'">
                    </div>
                </div>
            </div>
        </div>
        <form id="f-actualizar-mobile-${d.id}" action="${rutaActualizar}" method="POST" style="display:none;">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="PATCH">
        </form>`;
    }

    // ─── AJAX Scan ──────────────────────────────────────────────────────────────
    document.getElementById('formScan').addEventListener('submit', function (e) {
        e.preventDefault();

        const scanInput = document.getElementById('scan');
        const codigo    = scanInput.value.trim();
        if (!codigo) return;

        const spinner  = document.getElementById('scan-spinner');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        spinner.classList.remove('d-none');
        scanInput.disabled = true;

        fetch('{{ route("ventas.escanear") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ scan: codigo }),
        })
        .then(res => res.json())
        .then(data => {
            spinner.classList.add('d-none');
            scanInput.disabled = false;
            scanInput.value    = '';
            scanInput.focus();

            if (data.status === 'success') {
                mostrarToast(`<i class="fas fa-check-circle me-1"></i> ${data.message} — <strong>${data.detalle.nombre}</strong>`, 'success');

                // ── Actualizar resumen ──
                document.getElementById('resumen-cantidad').textContent = data.resumen.cantidad_productos;
                document.getElementById('resumen-total').textContent    = 'Q' + data.resumen.total_vendido;

                // ── Agregar fila usando la API de DataTables ──
                const d          = data.detalle;
                const csrfToken  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const totalFilas = dtTable.rows().count() + 1;

                const fotoHtml = d.foto
                    ? `<img src="${d.foto}" class="rounded-3 shadow-sm object-cover cursor-pointer"
                           style="width:45px;height:45px;" alt="Foto"
                           data-bs-toggle="modal" data-bs-target="#fotoModal"
                           onclick="mostrarImagen('${d.foto}')">`
                    : `<div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted"
                            style="width:45px;height:45px;"><i class="fas fa-image"></i></div>`;

                // Construir la fila como elemento jQuery (DataTables lo necesita así)
                const $fila = $(`<tr class="align-middle fila-nueva" id="fila-detalle-${d.id}">
                    <td>${d.id}</td>
                    <td class="d-none d-md-table-cell text-center"></td>
                    <td class="d-none d-lg-table-cell text-center">${d.codigo}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">${d.nombre}</span>
                            <small class="d-lg-none text-muted">${d.codigo}</small>
                        </div>
                    </td>
                    <td class="d-none d-sm-table-cell text-center">${fotoHtml}</td>
                    <td class="text-center">
                        <input type="number" name="cantidad" value="${d.cantidad}" min="1"
                            form="f-actualizar-desktop-${d.id}"
                            class="text-center fw-bold mx-auto"
                            style="max-width:60px;border:none;border-bottom:2px solid #ced4da;background:transparent;outline:none;padding:2px;"
                            onfocus="this.style.borderBottomColor='#0d6efd'"
                            onblur="this.style.borderBottomColor='#ced4da'">
                    </td>
                    <td class="d-none d-sm-table-cell text-center text-nowrap">Q${fmt(d.precio_unitario)}</td>
                    <td class="d-none d-md-table-cell text-center">
                        <input type="number" name="descuento" value="${fmt(d.descuento)}" step="0.01" min="0" max="${fmt(d.precio_unitario)}"
                            form="f-actualizar-desktop-${d.id}"
                            class="text-center mx-auto"
                            style="max-width:70px;border:none;border-bottom:2px solid #ced4da;background:transparent;outline:none;padding:2px;"
                            placeholder="0.00"
                            onfocus="this.style.borderBottomColor='#0d6efd'"
                            onblur="this.style.borderBottomColor='#ced4da'">
                    </td>
                    <td class="fw-bold text-center text-nowrap text-primary">Q${fmt(d.subtotal)}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button type="submit" form="f-actualizar-desktop-${d.id}"
                                class="btn btn-outline-success btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                                style="width:32px;height:32px;" title="Actualizar">
                                <i class="fas fa-check" style="font-size:14px;"></i>
                            </button>
                            <form action="/admin/ventas/${d.id}" method="POST" class="d-inline"
                                onsubmit="return confirm('¿Eliminar este producto de la venta?')">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit"
                                    class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                                    style="width:32px;height:32px;" title="Eliminar">
                                    <i class="fas fa-trash-alt" style="font-size:14px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>`);

                // Agregar a DataTables y renderizar (draw false = no resetea la paginación)
                const newRow = dtTable.row.add($fila).draw(false);

                // Agregar el formulario PATCH fuera de la tabla (después del wrapper)
                $('#tablaProductos').closest('.table-responsive').after(
                    `<form id="f-actualizar-desktop-${d.id}" action="/admin/ventas/${d.id}" method="POST" style="display:none;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PATCH">
                    </form>`
                );

                dtTable.columns.adjust();

                // ── Agregar card móvil ──
                const mobileContainer = document.querySelector('.d-md-none.space-y-3');
                if (mobileContainer) {
                    mobileContainer.insertAdjacentHTML('afterbegin', buildCard(data.detalle));
                }

                // ── Highlight en la fila nueva (parpadeo verde) ──
                setTimeout(() => {
                    const filaNode = newRow.node();
                    if (filaNode) {
                        filaNode.style.transition = 'background-color 0.6s';
                        filaNode.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => { filaNode.style.backgroundColor = ''; }, 1400);
                    }
                    // También en las cards móviles
                    document.querySelectorAll('.fila-nueva').forEach(el => {
                        el.style.transition = 'background-color 0.6s';
                        el.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => { el.style.backgroundColor = ''; el.classList.remove('fila-nueva'); }, 1400);
                    });
                }, 80);

            } else if (data.status === 'not_found') {
                mostrarToast(`<i class="fas fa-exclamation-triangle me-1"></i> ${data.message}`, 'warning');

                // Agregar código no encontrado al panel lateral
                const listaCodigosNoEncontrados = document.getElementById('lista-codigos-no-encontrados');
                const emptyMsg = document.getElementById('codigos-no-encontrados-empty');
                if (emptyMsg) emptyMsg.remove();
                if (listaCodigosNoEncontrados) {
                    const csrfToken2 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    listaCodigosNoEncontrados.insertAdjacentHTML('beforeend',
                        `<div class="list-group-item p-3 border-start border-4 border-warning">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 me-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-dark me-2"><i class="fas fa-barcode"></i></span>
                                        <strong class="text-dark">${data.codigo}</strong>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="https://www.google.com/search?q=${encodeURIComponent(data.codigo)}"
                                        target="_blank" class="btn btn-sm btn-outline-primary d-flex align-items-center" title="Buscar en Google">
                                        <i class="fab fa-google me-1"></i>
                                    </a>
                                    <a href="/admin/productos/create?codigo=${encodeURIComponent(data.codigo)}"
                                        target="_blank" class="btn btn-sm btn-success d-flex align-items-center" title="Crear Producto">
                                        <i class="fas fa-plus-circle me-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>`
                    );
                    // Actualizar badge de cantidad y mostrarlo si estaba oculto
                    const badge = document.getElementById('badge-codigos');
                    if (badge) {
                        badge.classList.remove('d-none');
                        badge.textContent = parseInt(badge.textContent || '0') + 1;
                    }
                    // Mostrar la lista si estaba oculta
                    if (listaCodigosNoEncontrados) {
                        listaCodigosNoEncontrados.style.display = '';
                    }
                }

            } else {
                mostrarToast(`<i class="fas fa-times-circle me-1"></i> ${data.message}`, 'danger');
            }
        })
        .catch(() => {
            spinner.classList.add('d-none');
            scanInput.disabled = false;
            scanInput.value    = '';
            scanInput.focus();
            mostrarToast('<i class="fas fa-times-circle me-1"></i> Error de conexión. Intenta de nuevo.', 'danger');
        });
    });
</script>