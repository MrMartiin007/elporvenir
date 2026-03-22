<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- ─────────────────────────────────────────────────────────────
                 Header + Filtros
            ───────────────────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Estado del Inventario</h3>
                        <p class="text-sm text-gray-500">Análisis de stock y valorización al precio de costo</p>
                    </div>

                    <form method="GET" action="{{ route('inventario.index') }}" class="flex flex-wrap items-center gap-3">
                        {{-- Año --}}
                        <select name="anio"
                            class="block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm"
                            onchange="this.form.submit()">
                            @foreach($aniosDisponibles as $a)
                                <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                            @if($aniosDisponibles->isEmpty())
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                            @endif
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
                    </form>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 KPI Cards — Fila 1
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
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Valorización</span>
                    </div>
                    <div class="text-3xl font-bold text-green-600">Q{{ number_format($valorInventario, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Valor total al precio de costo</div>
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
                 KPI Cards — Fila 2: Entradas del período
            ───────────────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                {{-- Total Invertido en el período --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-purple-50 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Inversión</span>
                    </div>
                    <div class="text-3xl font-bold text-purple-600">Q{{ number_format($totalEntradasPeriodo, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">
                        Invertido en {{ $mes === 'todos' ? $anio : ($mesesNombre[(int)$mes] ?? '') . ' ' . $anio }}
                    </div>
                </div>

                {{-- Cantidad de entradas en el período --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Entradas</span>
                    </div>
                    <div class="text-3xl font-bold text-yellow-600">{{ number_format($cantidadEntradasPeriodo) }}</div>
                    <div class="mt-2 text-sm text-gray-500">
                        Registros de entrada en el período
                    </div>
                </div>

                {{-- Mejor período --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-green-50 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Mayor ingreso</span>
                    </div>
                    @if($mejorMonto > 0)
                        <div class="text-2xl font-bold text-gray-900 truncate">{{ $mejorLabel }}</div>
                        <div class="mt-2 text-sm text-green-600">Q{{ number_format($mejorMonto, 2) }}</div>
                    @else
                        <div class="text-xl font-bold text-gray-400">Sin datos</div>
                    @endif
                </div>

                {{-- Peor período --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-red-50 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menor ingreso</span>
                    </div>
                    @if($peorMonto > 0)
                        <div class="text-2xl font-bold text-gray-900 truncate">{{ $peorLabel }}</div>
                        <div class="mt-2 text-sm text-red-600">Q{{ number_format($peorMonto, 2) }}</div>
                    @else
                        <div class="text-xl font-bold text-gray-400">Sin datos</div>
                    @endif
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 Gráficas
            ───────────────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                {{-- Gráfica Principal: Inversión por período (2/3) --}}
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">
                            @if($mes === 'todos')
                                Inversión en Entradas — Año {{ $anio }}
                            @else
                                Inversión en Entradas — {{ $mesesNombre[(int)$mes] ?? '' }} {{ $anio }}
                            @endif
                        </h3>
                        <span class="text-xs text-gray-400 border border-gray-200 rounded-full px-3 py-1">
                            Precio Costo (Q)
                        </span>
                    </div>
                    <div class="relative h-80 w-full">
                        <canvas id="costoChart"></canvas>
                    </div>
                </div>

                {{-- Gráfica Donut: Stock por marca (1/3) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Stock por Marca</h3>
                    </div>
                    <div class="relative h-56 w-full flex items-center justify-center">
                        @if($stockPorMarca->isNotEmpty())
                            <canvas id="marcaChart"></canvas>
                        @else
                            <p class="text-gray-400 text-sm">Sin datos de stock por marca.</p>
                        @endif
                    </div>
                    @if($stockPorMarca->isNotEmpty())
                        <div class="mt-4 space-y-2" style="max-height: 150px; overflow-y: auto;">
                            @foreach($stockPorMarca->take(6) as $item)
                                <div class="flex items-center justify-between px-2 py-1 rounded hover:bg-gray-50">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-2 h-2 rounded-full flex-shrink-0 bg-indigo-500"></div>
                                        <span class="text-sm text-gray-700 truncate">{{ $item['marca'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 ml-2 flex-shrink-0">
                                        {{ number_format($item['stock']) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 Top 10 productos con más stock
            ───────────────────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Top 10 Productos con Mayor Stock
                </h3>

                @if($topProductos->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">#</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Código</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Producto</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Marca</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Stock</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Precio Costo</th>
                                    <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topProductos as $i => $producto)
                                    @php
                                        $precioCosto = optional($producto->ultimaEntrada)->precio_costo ?? 0;
                                        $valorTotal  = $producto->stock * (float) $precioCosto;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 text-xs font-bold">
                                                {{ $i + 1 }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="font-mono text-xs text-gray-500">{{ $producto->codigo_producto }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="font-medium text-gray-800">{{ $producto->detalle_producto }}</span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                                                {{ optional($producto->marca)->nombre_marca ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <span class="font-bold text-blue-600">{{ number_format($producto->stock) }}</span>
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <span class="text-gray-700">Q{{ number_format((float)$precioCosto, 2) }}</span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <span class="font-bold text-green-600">Q{{ number_format($valorTotal, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-8">No hay productos con stock disponible.</p>
                @endif
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 Alertas: Productos con stock = 0
            ───────────────────────────────────────────────────────────────── --}}
            @if($productosEnCero->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                <h3 class="text-lg font-bold text-red-700 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Productos sin Stock ({{ $cantidadEnCero }})
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-red-50">
                                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Código</th>
                                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Producto</th>
                                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Marca</th>
                                <th class="pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-50">
                            @foreach($productosEnCero as $producto)
                                <tr class="hover:bg-red-50 transition">
                                    <td class="py-3 pr-4">
                                        <span class="font-mono text-xs text-gray-500">{{ $producto->codigo_producto }}</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="font-medium text-gray-800">{{ $producto->detalle_producto }}</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-block bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">
                                            {{ optional($producto->marca)->nombre_marca ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('entradas.create', ['productos_id' => $producto->id]) }}"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Agregar entrada
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

    {{-- ─────────────────────────────────────────────────────────────────────
         Chart.js Scripts
    ──────────────────────────────────────────────────────────────────────── --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Chart 1: Inversión en entradas por período ─────────────────
            const costoCtx = document.getElementById('costoChart').getContext('2d');
            const costoData   = @json(array_values($datosGraficoCosto));
            const costoLabels = @json($labelsGrafico);

            let gradientCosto = costoCtx.createLinearGradient(0, 0, 0, 350);
            gradientCosto.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
            gradientCosto.addColorStop(1, 'rgba(99, 102, 241, 0)');

            new Chart(costoCtx, {
                type: 'line',
                data: {
                    labels: costoLabels,
                    datasets: [{
                        label: 'Inversión (Q)',
                        data: costoData,
                        borderColor: '#6366F1',
                        backgroundColor: gradientCosto,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366F1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { family: "'Inter', sans-serif", size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    let label = ctx.dataset.label || '';
                                    if (label) label += ': ';
                                    if (ctx.parsed.y !== null) {
                                        label += 'Q' + new Intl.NumberFormat('es-GT').format(ctx.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F3F4F6', drawBorder: false },
                            ticks: {
                                callback: function (v) { return 'Q' + v; },
                                font: { family: "'Inter', sans-serif" }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Inter', sans-serif" } }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });

            // ── Chart 2: Stock por Marca (Donut) ───────────────────────────
            const marcaCtxRaw = document.getElementById('marcaChart');
            if (marcaCtxRaw) {
                const marcaCtx = marcaCtxRaw.getContext('2d');
                const marcaData   = @json($stockPorMarca);
                const marcaLabels = marcaData.map(function(m) { return m.marca; });
                const marcaValues = marcaData.map(function(m) { return m.stock; });

                new Chart(marcaCtx, {
                    type: 'doughnut',
                    data: {
                        labels: marcaLabels,
                        datasets: [{
                            data: marcaValues,
                            backgroundColor: [
                                '#6366F1','#10B981','#F59E0B','#EF4444',
                                '#8B5CF6','#EC4899','#14B8A6','#F97316'
                            ],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                        return ctx.label + ': ' + new Intl.NumberFormat('es-GT').format(ctx.raw) + ' uds.';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
