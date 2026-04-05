<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🔍 Escanear Producto - Auditoría Abierta') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <script>
            Swal.fire({
                title: '{{ session("success") }}',
                icon: 'success',
                didClose: () => {
                    const input = document.getElementById('codigoBuscar');
                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>Swal.fire('{{ session("error") }}', '', 'error');</script>
    @endif

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        {{-- Scanner --}}
        <div class="mb-4 bg-white shadow-sm sm:rounded-lg p-4">
            <form method="GET" action="{{ route('auditoria.create') }}" class="flex gap-3">
                <input type="text" name="buscar" id="codigoBuscar" class="border border-gray-300 rounded form-control"
                    placeholder="Escanee o escriba el código del producto para auditar" autofocus autocomplete="off" />
            </form>
        </div>

        {{-- Modal: producto encontrado --}}
        @if(request('buscar') && $productosFiltrados->count() === 1 && $productosFiltrados->first()->codigo_producto === request('buscar'))
            @php
                $producto = $productosFiltrados->first();
                $costo = $ultimaEntrada?->precio_costo ?? 0;
                $venta = $ultimaEntrada?->precio_venta ?? 0;
                $docena = $ultimaEntrada?->precio_docena ?? 0;
            @endphp

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = new bootstrap.Modal(document.getElementById('modalAuditoria'));
                    modal.show();
                    setTimeout(() => document.getElementById('stock_nuevo')?.focus(), 500);
                });
            </script>

            <div class="modal fade show d-block" id="modalAuditoria" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border-radius:12px;">
                        <div class="modal-header bg-warning bg-opacity-25">
                            <h5 class="modal-title fw-bold">🔍 Auditar Producto</h5>
                            <a href="{{ route('auditoria.create') }}" class="btn-close"></a>
                        </div>
                        <div class="modal-body">
                            {{-- Foto e info básica --}}
                            <div class="text-center mb-3">
                                @if($producto->foto_producto)
                                    <img src="{{ asset('storage/' . $producto->foto_producto) }}" alt="Foto" class="mx-auto"
                                        style="height:100px; border-radius:8px;">
                                @else
                                    <img src="{{ asset('images/sin-foto.png') }}" alt="Sin foto" class="mx-auto"
                                        style="height:100px; border-radius:8px;">
                                @endif
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <x-input-label for="codigo_display" :value="__('Código')" />
                                    <x-text-input id="codigo_display" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->codigo_producto }}" disabled />
                                </div>
                                <div class="col">
                                    <x-input-label for="detalle_display" :value="__('Nombre')" />
                                    <x-text-input id="detalle_display" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->detalle_producto }}" disabled />
                                </div>
                                <div class="col">
                                    <x-input-label for="marca_display" :value="__('Marca')" />
                                    <x-text-input id="marca_display" type="text" class="mt-1 block w-full"
                                        value="{{ $producto->marca?->nombre_marca ?? '—' }}" disabled />
                                </div>
                            </div>

                            <form action="{{ route('auditoria.store') }}" method="POST" class="guardar">
                                @csrf
                                <input type="hidden" name="productos_id" value="{{ $producto->id }}">

                                {{-- Stock --}}
                                <div class="mb-3 p-3 rounded" style="background: #fff8e1; border: 1px solid #ffe082;">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <x-input-label for="stock_actual_display" :value="__('Stock en sistema')" />
                                            <x-text-input id="stock_actual_display" type="number" class="mt-1 block w-full"
                                                value="{{ $producto->stock }}" disabled />
                                        </div>
                                        <div
                                            class="col-md-4 text-center d-flex align-items-center justify-content-center pt-3">
                                            <span class="fs-4">→</span>
                                        </div>
                                        <div class="col-md-4">
                                            <x-input-label for="stock_nuevo" :value="__('Stock REAL contado ✏️')" />
                                            <x-text-input id="stock_nuevo" name="stock_nuevo" type="number" min="0"
                                                class="mt-1 block w-full border-2 border-yellow-400"
                                                value="{{ $producto->stock }}" required />
                                        </div>
                                    </div>
                                </div>

                                {{-- Precios --}}
                                <div class="row mb-3">
                                    <div class="col">
                                        <x-input-label for="precio_costo" :value="__('Precio Costo')" />
                                        <x-text-input id="precio_costo" name="precio_costo" type="number" step="0.01"
                                            class="mt-1 block w-full" value="{{ $costo }}" required />
                                    </div>
                                    <div class="col">
                                        <x-input-label for="precio_venta" :value="__('Precio Venta')" />
                                        <x-text-input id="precio_venta" name="precio_venta" type="number" step="0.01"
                                            class="mt-1 block w-full" value="{{ $venta }}" required />
                                    </div>
                                    <div class="col">
                                        <x-input-label for="precio_docena" :value="__('Precio Docena')" />
                                        <x-text-input id="precio_docena" name="precio_docena" type="number" step="0.01"
                                            class="mt-1 block w-full" value="{{ $docena }}" required />
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <a href="{{ route('auditoria.create') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-warning fw-bold">✅ Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Tabla Izquierda --}}
            <div class="col-lg-9 mb-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">📋 Registros de esta sesión</h5>
                    </div>
                    <div class="table-responsive-md">
                        <table class="table table-hover table-bordered table-sm" id="tablaDetalleAuditorias">
                            <thead class="text-center table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Stock</th>
                                    <th>Precio Costo</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditoria->detalles as $detalle)
                                    @php
                                        $subtotal = $detalle->stock_nuevo * $detalle->precio_costo_nuevo;
                                    @endphp
                                    <tr class="align-middle text-center">
                                        <td class="font-mono text-xs">{{ $detalle->producto?->codigo_producto }}</td>
                                        <td class="text-start">
                                            <strong>{{ $detalle->producto?->detalle_producto ?? '—' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge {{ $detalle->stock_anterior != $detalle->stock_nuevo ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $detalle->stock_nuevo }}
                                            </span>
                                        </td>
                                        <td>Q {{ number_format($detalle->precio_costo_nuevo, 2) }}</td>
                                        <td class="fw-bold text-success">Q {{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Card Resumen Derecha --}}
            <div class="col-lg-3">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 position-sticky" style="top: 20px;">
                    <h5 class="fw-bold mb-4 text-center">Resumen de Sesión</h5>
                    @php
                        $sumTotal = 0;
                        foreach($auditoria->detalles as $d) {
                            $sumTotal += ($d->stock_nuevo * $d->precio_costo_nuevo);
                        }
                    @endphp
                    
                    <div class="mb-3">
                        <span class="text-muted d-block text-uppercase text-xs fw-bold">Fecha</span>
                        <span class="fs-6 d-block">{{ \Carbon\Carbon::parse($auditoria->fecha_auditoria)->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="mb-3 border-top pt-3">
                        <span class="text-muted d-block text-uppercase text-xs fw-bold">Productos Auditados</span>
                        <span class="fs-4 d-block text-primary fw-bold">{{ $auditoria->cantidad_productos }}</span>
                    </div>

                    <div class="mb-4 border-top pt-3">
                        <span class="text-muted d-block text-uppercase text-xs fw-bold">Total Valorado (Costo)</span>
                        <span class="fs-4 d-block text-success fw-bold">Q {{ number_format($sumTotal, 2) }}</span>
                    </div>

                    <div class="border-top pt-3">
                        <form action="{{ route('auditoria.cerrar', $auditoria->id) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de que quieres cerrar esta auditoría? Ya no podrás editarla.');">
                            @csrf
                            <button type="submit" class="btn btn-danger fw-bold w-100 py-2">
                                ❌ Cerrar Auditoría
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if ($.fn.DataTable.isDataTable('#tablaDetalleAuditorias')) {
                $('#tablaDetalleAuditorias').DataTable().destroy();
            }
            $('#tablaDetalleAuditorias').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "order": [], // Let the backend feed it descending automatically
                "pageLength": 10
            });
        });

        document.querySelectorAll('.guardar').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
                    return;
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Guardando auditoría...',
                    text: 'Por favor espera un momento',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            });
        });

        const formatCurrencyInputs = ['precio_costo', 'precio_venta', 'precio_docena'];

        formatCurrencyInputs.forEach(id => {
            const input = document.getElementById(id);
            if (!input) return;
            if (input.value.trim() === '') input.value = '0.00';

            input.addEventListener('input', () => {
                let raw = input.value.replace(/[^0-9]/g, '');
                if (raw === '') { input.value = '0.'; return; }
                let num = parseInt(raw, 10);
                if (isNaN(num)) num = 0;
                input.value = (num / 100).toFixed(2);
            });

            input.addEventListener('blur', () => {
                if (input.value.trim() === '' || isNaN(parseFloat(input.value))) {
                    input.value = '0.00';
                }
            });
        });

        const inputCosto = document.getElementById('precio_costo');
        const inputVenta = document.getElementById('precio_venta');
        const inputDocena = document.getElementById('precio_docena');

        function actualizarPrecios() {
            let costo = parseFloat(inputCosto.value);
            if (isNaN(costo)) costo = 0;
            inputVenta.value = (costo * 1.25).toFixed(2);
            inputDocena.value = (costo * 1.15).toFixed(2);
        }

        if (inputCosto) {
            inputCosto.addEventListener('input', actualizarPrecios);
            inputCosto.addEventListener('blur', () => {
                if (inputCosto.value.trim() === '' || isNaN(parseFloat(inputCosto.value))) {
                    inputCosto.value = '0.00';
                }
                actualizarPrecios();
            });
            if (inputCosto.value.trim() === '') inputCosto.value = '0.00';
        }

    </script>
</x-app-layout>