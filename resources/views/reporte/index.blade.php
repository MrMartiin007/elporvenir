<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportes y Estadísticas') }}
        </h2>
    </x-slot>

    <!-- Usamos container-fluid o w-full con px-4/6 para ocupar mas espacio -->
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <!-- Header & Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Resumen de Ventas</h3>
                        <p class="text-sm text-gray-500">Analiza el rendimiento de tu negocio</p>
                    </div>

                    <form method="GET" action="{{ route('reportes.index') }}" class="flex flex-wrap items-center gap-3">
                        <!-- Año -->
                        <div class="relative">
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
                        </div>

                        <!-- Mes -->
                        <div class="relative">
                            <select name="mes"
                                class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm"
                                onchange="this.form.submit()">
                                <option value="todos" {{ $mes == 'todos' ? 'selected' : '' }}>Todos los meses</option>
                                <option value="1" {{ $mes == '1' ? 'selected' : '' }}>Enero</option>
                                <option value="2" {{ $mes == '2' ? 'selected' : '' }}>Febrero</option>
                                <option value="3" {{ $mes == '3' ? 'selected' : '' }}>Marzo</option>
                                <option value="4" {{ $mes == '4' ? 'selected' : '' }}>Abril</option>
                                <option value="5" {{ $mes == '5' ? 'selected' : '' }}>Mayo</option>
                                <option value="6" {{ $mes == '6' ? 'selected' : '' }}>Junio</option>
                                <option value="7" {{ $mes == '7' ? 'selected' : '' }}>Julio</option>
                                <option value="8" {{ $mes == '8' ? 'selected' : '' }}>Agosto</option>
                                <option value="9" {{ $mes == '9' ? 'selected' : '' }}>Septiembre</option>
                                <option value="10" {{ $mes == '10' ? 'selected' : '' }}>Octubre</option>
                                <option value="11" {{ $mes == '11' ? 'selected' : '' }}>Noviembre</option>
                                <option value="12" {{ $mes == '12' ? 'selected' : '' }}>Diciembre</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- KPI Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Ventas Totales -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-indigo-50 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ventas</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">Q{{ number_format($totalVentas, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Total vendido (tienda + online)</div>
                </div>

                <!-- Ganancia Estimada -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-green-50 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ganancia</span>
                    </div>
                    <div class="text-3xl font-bold text-green-600">Q{{ number_format($gananciaTotal, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Beneficio estimado total</div>
                </div>

                <!-- Mejor Rendimiento -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Mejor momento</span>
                    </div>
                    @if($mejorMonto > 0)
                        <div class="text-2xl font-bold text-gray-900 truncate">{{ $mejorLabel }}</div>
                        <div class="mt-2 text-sm text-green-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            Q{{ number_format($mejorMonto, 2) }}
                        </div>
                    @else
                        <div class="text-xl font-bold text-gray-500">Sin datos</div>
                    @endif
                </div>

                <!-- Menor Rendimiento -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-red-50 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menor momento</span>
                    </div>
                    @if($peorMonto >= 0)
                        <div class="text-2xl font-bold text-gray-900 truncate">{{ $peorLabel }}</div>
                        <div class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            Q{{ number_format($peorMonto, 2) }}
                        </div>
                    @else
                        <div class="text-xl font-bold text-gray-500">Sin datos</div>
                    @endif
                </div>
            </div>

            <!-- Charts Section: Split View -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Sales Chart (Occupies 2/3) -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">
                            @if($mes == 'todos')
                                Comportamiento Anual {{ $anio }}
                            @else
                                Comportamiento Diario - Mes {{ $mes }}/{{ $anio }}
                            @endif
                        </h3>
                    </div>
                    <div class="relative h-96 w-full">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>

                <!-- User Performance Chart (Occupies 1/3) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Rendimiento por Usuario</h3>
                    </div>
                    <div class="relative h-64 w-full flex items-center justify-center">
                        @if(count($ventasPorUsuario) > 0)
                            <canvas id="userChart"></canvas>
                        @else
                            <p class="text-gray-500">No hay datos de ventas por usuario.</p>
                        @endif
                    </div>

                    <!-- Small Legend/List below chart -->
                    @if(count($ventasPorUsuario) > 0)
                        <div class="mt-6 space-y-3">
                            @foreach($ventasPorUsuario as $user => $monto)
                                <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $user }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">Q{{ number_format($monto, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Pedidos por Estado -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <svg class="w-5 h-5 inline mr-2 text-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Resumen de Pedidos Online
                </h3>
                @php
                    $estadosConfig = [
                        'pendiente' => ['label' => 'Pendientes', 'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'confirmado' => ['label' => 'Confirmados', 'color' => 'blue', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'en_proceso' => ['label' => 'En Proceso', 'color' => 'indigo', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        'enviado' => ['label' => 'Enviados', 'color' => 'purple', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                        'entregado' => ['label' => 'Entregados', 'color' => 'green', 'icon' => 'M5 13l4 4L19 7'],
                        'cancelado' => ['label' => 'Cancelados', 'color' => 'red', 'icon' => 'M6 18L18 6M6 6l12 12'],
                    ];
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($estadosConfig as $estado => $config)
                        @php
                            $data = $pedidosPorEstado[$estado] ?? null;
                            $cantidad = $data ? $data->total : 0;
                            $monto = $data ? $data->monto : 0;
                        @endphp
                        <div
                            class="text-center p-4 rounded-lg bg-{{ $config['color'] }}-50 border border-{{ $config['color'] }}-100">
                            <svg class="w-6 h-6 mx-auto mb-2 text-{{ $config['color'] }}-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $config['icon'] }}"></path>
                            </svg>
                            <div class="text-2xl font-bold text-{{ $config['color'] }}-700">
                                {{ number_format($cantidad, 0) }}</div>
                            <div class="text-xs font-medium text-{{ $config['color'] }}-600 mb-1">{{ $config['label'] }}
                            </div>
                            <div class="text-xs text-gray-500">Q{{ number_format($monto, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Config -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Chart 1: Main Sales ---
            const ctx = document.getElementById('mainChart').getContext('2d');
            const rawData = @json(array_values($datosGrafico));
            const displayLabels = @json($labelsGrafico);

            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            let gradientPedidos = ctx.createLinearGradient(0, 0, 0, 400);
            gradientPedidos.addColorStop(0, 'rgba(147, 51, 234, 0.2)');
            gradientPedidos.addColorStop(1, 'rgba(147, 51, 234, 0)');

            const pedidosData = @json(array_values($datosPedidosGrafico));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayLabels,
                    datasets: [{
                        label: 'Ventas Tienda (Q)',
                        data: rawData,
                        borderColor: '#4F46E5',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4F46E5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Pedidos Online (Q)',
                        data: pedidosData,
                        borderColor: '#9333EA',
                        backgroundColor: gradientPedidos,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#9333EA',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                        borderDash: [5, 5]
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
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) {
                                        label += 'Q' + new Intl.NumberFormat('es-GT').format(context.parsed.y);
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
                                callback: function (value) { return 'Q' + value; },
                                font: { family: "'Inter', sans-serif" }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Inter', sans-serif" } }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' },
                }
            });

            // --- Chart 2: User Performance ---
            const userCtxRaw = document.getElementById('userChart');
            if (userCtxRaw) {
                const userCtx = userCtxRaw.getContext('2d');
                const userData = @json($ventasPorUsuario);
                const userLabels = Object.keys(userData);
                const userValues = Object.values(userData);

                new Chart(userCtx, {
                    type: 'doughnut',
                    data: {
                        labels: userLabels,
                        datasets: [{
                            data: userValues,
                            backgroundColor: [
                                '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }, // Custom legend used
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.label || '';
                                        if (label) { label += ': '; }
                                        label += 'Q' + new Intl.NumberFormat('es-GT').format(context.raw);
                                        return label;
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