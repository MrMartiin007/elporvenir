<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventario') }}
        </h2>
    </x-slot>

    {{-- jQuery + DataTables CSS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        /* Ajustes visuales para DataTable con Tailwind */
        .dataTables_wrapper .row { margin-bottom: 1rem; }
        .dataTables_filter input { border-radius: 0.5rem; border: 1px solid #e5e7eb; padding: 0.375rem 0.75rem; margin-left: 0.5rem; outline: none; }
        .dataTables_filter input:focus { border-color: #6366f1; ring: 1px #6366f1; }
        .dataTables_length select { border-radius: 0.5rem; border: 1px solid #e5e7eb; padding: 0.375rem 2rem 0.375rem 0.75rem; outline: none; }
        table.dataTable.no-footer { border-bottom: 1px solid #f3f4f6; }
        div.dataTables_info { color: #6b7280; font-size: 0.875rem; padding-top: 1rem; }
        .page-item.active .page-link { background-color: #6366f1; border-color: #6366f1; }
        .page-link { color: #4b5563; }
    </style>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- ─────────────────────────────────────────────────────────────
                 Header + Filtros
            ───────────────────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Estado del Inventario</h3>
                        <p class="text-sm text-gray-500">Análisis de stock y valor ingresado al inventario</p>
                    </div>

                    <form method="GET" action="{{ route('inventario.index') }}" class="flex flex-wrap items-center gap-3">
                        {{-- Año --}}
                        <select name="anio"
                            class="block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm"
                            onchange="this.form.submit()">
                            <option value="todos" {{ $anio === 'todos' ? 'selected' : '' }}>Todos los años</option>
                            @foreach($aniosDisponibles as $a)
                                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>

                        {{-- Mes --}}
                        <select name="mes"
                            class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm"
                            onchange="this.form.submit()">
                            <option value="todos" {{ $mes == 'todos' ? 'selected' : '' }}>Todos los meses</option>
                            <option value="1"  {{ $mes == '1'  ? 'selected' : '' }}>Enero</option>
                            <option value="2"  {{ $mes == '2'  ? 'selected' : '' }}>Febrero</option>
                            <option value="3"  {{ $mes == '3'  ? 'selected' : '' }}>Marzo</option>
                            <option value="4"  {{ $mes == '4'  ? 'selected' : '' }}>Abril</option>
                            <option value="5"  {{ $mes == '5'  ? 'selected' : '' }}>Mayo</option>
                            <option value="6"  {{ $mes == '6'  ? 'selected' : '' }}>Junio</option>
                            <option value="7"  {{ $mes == '7'  ? 'selected' : '' }}>Julio</option>
                            <option value="8"  {{ $mes == '8'  ? 'selected' : '' }}>Agosto</option>
                            <option value="9"  {{ $mes == '9'  ? 'selected' : '' }}>Septiembre</option>
                            <option value="10" {{ $mes == '10' ? 'selected' : '' }}>Octubre</option>
                            <option value="11" {{ $mes == '11' ? 'selected' : '' }}>Noviembre</option>
                            <option value="12" {{ $mes == '12' ? 'selected' : '' }}>Diciembre</option>
                        </select>

                        <button type="submit" formaction="{{ route('inventario.exportar') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm ml-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Descargar Excel
                        </button>
                    </form>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 KPI Cards (Sólo la fila principal)
            ───────────────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Card 1: Total Productos --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-indigo-50 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Productos</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($totalProductos) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Referencias registradas</div>
                </div>

                {{-- Card 2: Stock Total --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stock</span>
                    </div>
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($stockTotal) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Unidades totales en bodega</div>
                </div>

                {{-- Card 3: Valor del Inventario --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-green-50 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Valor Actual</span>
                    </div>
                    <div class="text-3xl font-bold text-green-600">Q{{ number_format($valorInventario, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Valor general al precio de costo</div>
                </div>

                {{-- Card 4: Alertas Stock Cero --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full {{ $cantidadEnCero > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alertas</span>
                    </div>
                    <div class="text-3xl font-bold {{ $cantidadEnCero > 0 ? 'text-red-600' : 'text-gray-400' }}">
                        {{ number_format($cantidadEnCero) }}
                    </div>
                    <div class="mt-2 text-sm text-gray-500">Productos con stock = 0</div>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 Tabla: Top productos con mayor stock
            ───────────────────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-indigo-100 text-indigo-700 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </span>
                    Productos con Mayor Stock
                </h3>

                @if($topProductos->isNotEmpty())
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table id="tablaTopStock" class="w-full text-sm text-left text-gray-600" style="width:100%">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Código</th>
                                    <th class="px-6 py-4 font-semibold">Producto</th>
                                    <th class="px-6 py-4 font-semibold">Marca</th>
                                    <th class="px-6 py-4 font-semibold text-right">Stock Actual</th>
                                    <th class="px-6 py-4 font-semibold text-right">Precio Costo</th>
                                    <th class="px-6 py-4 font-semibold text-right">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProductos as $producto)
                                    @php
                                        $precioCosto = (float) optional($producto->ultimaEntrada)->precio_costo;
                                        $valorTotal  = (int) $producto->stock * $precioCosto;
                                    @endphp
                                    <tr class="bg-white border-b hover:bg-indigo-50/40 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $producto->codigo_producto }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-800">{{ $producto->detalle_producto }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ optional($producto->marca)->nombre_marca ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center justify-center w-12 h-6 rounded bg-indigo-50 text-indigo-700 font-bold text-sm">
                                                {{ number_format((int)$producto->stock) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right tabular-nums text-gray-600">
                                            Q{{ number_format($precioCosto, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold tabular-nums text-emerald-600">
                                            Q{{ number_format($valorTotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-sm">No hay productos con stock disponible.</p>
                    </div>
                @endif
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 Tabla: Productos sin stock
            ───────────────────────────────────────────────────────────────── --}}
            @if($productosEnCero->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                <h3 class="text-lg font-bold text-red-700 mb-6 flex items-center">
                    <span class="bg-red-100 text-red-600 p-2 rounded-lg mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    Productos Agotados ({{ $cantidadEnCero }})
                </h3>
                <div class="overflow-x-auto rounded-lg border border-red-100">
                    <table id="tablaSinStock" class="w-full text-sm text-left text-gray-600" style="width:100%">
                        <thead class="text-xs text-red-800 uppercase bg-red-50 border-b border-red-100">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Código</th>
                                <th class="px-6 py-4 font-semibold">Producto</th>
                                <th class="px-6 py-4 font-semibold">Marca</th>
                                <th class="px-6 py-4 font-semibold text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosEnCero as $producto)
                                <tr class="bg-white border-b border-red-50 hover:bg-red-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $producto->codigo_producto }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $producto->detalle_producto }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ optional($producto->marca)->nombre_marca ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('entradas.create', ['productos_id' => $producto->id]) }}"
                                            class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Reponer
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ───────────────────────────────────────────────────────────────────────
         Scripts: Chart.js + DataTables
    ──────────────────────────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── DataTables Inicialización con Opciones ────────────────────────
            const dtOptions = {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"text-sm"l><"text-sm w-full md:w-auto mt-2 md:mt-0"f>>rt<"flex flex-col md:flex-row justify-between items-center mt-4"<"text-sm text-gray-500"i><"mt-2 md:mt-0"p>>',
            };

            if (document.getElementById('tablaTopStock')) {
                $('#tablaTopStock').DataTable({
                    ...dtOptions,
                    order: [[3, 'desc']], // ordenar por Stock descendente
                    columnDefs: [
                        { orderable: false, targets: [] },
                        { className: 'text-right', targets: [3, 4, 5] }
                    ]
                });
            }

            if (document.getElementById('tablaSinStock')) {
                $('#tablaSinStock').DataTable({
                    ...dtOptions,
                    order: [[1, 'asc']], // ordenar por nombre de producto
                    columnDefs: [
                        { orderable: false, targets: [3] }, 
                        { className: 'text-right', targets: [3] }
                    ]
                });
            }

        });
    </script>
</x-app-layout>
