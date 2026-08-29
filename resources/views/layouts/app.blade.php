<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$tittle ?? 'Elporvenir'}}</title>
    <link rel="shortcut icon" href="{{asset('logo.jpg')}}" type="image/x-icon">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />





    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="d-flex" style="height: 100vh; overflow: hidden;">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-grow-1 bg-gray-100 overflow-auto">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div>
                            {{ $header }}
                        </div>

                        <div class="flex items-center gap-4">
                            {{-- Configuración de Envío --}}
                            <div class="relative">
                                <button id="tarifaBtn"
                                    class="flex items-center text-gray-500 hover:text-gray-700 focus:outline-none transition"
                                    onclick="toggleTarifaDropdown()">
                                    <div
                                        class="flex items-center bg-gray-100 rounded-full px-3 py-1 border border-gray-200 hover:bg-gray-200">
                                        <i class="fas fa-truck text-blue-500 mr-2"></i>
                                        <span
                                            class="text-sm font-bold text-gray-700">Q.{{ isset($tarifaActual) ? number_format($tarifaActual->costo, 2) : '0.00' }}</span>
                                        <i class="fas fa-chevron-down text-xs ml-2 text-gray-400"></i>
                                    </div>
                                </button>

                                {{-- Dropdown Form --}}
                                <div id="tarifaDropdown"
                                    class="hidden absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100 ring-1 ring-black ring-opacity-5">
                                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                        <p class="text-sm font-medium text-gray-900">Tarifa de Envío</p>
                                        <p class="text-xs text-gray-500">Define el costo global de envío.</p>
                                    </div>
                                    <div class="p-4">
                                        <form action="{{ route('configuracion.tarifa.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="costoEnvio"
                                                    class="block text-xs font-medium text-gray-700 mb-1">Nuevo Costo
                                                    (Q)</label>
                                                <div class="relative rounded-md shadow-sm">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">Q</span>
                                                    </div>
                                                    <input type="number" name="costo" id="costoEnvio" step="0.01" min="0"
                                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md"
                                                        placeholder="0.00" required>
                                                </div>
                                            </div>
                                            <button type="submit"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs transition duration-150 ease-in-out">
                                                Actualizar Tarifa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Notificación de Pedidos --}}
                            <div class="relative">
                                <a href="{{ route('pedidos.index') }}" class="text-gray-500 hover:text-gray-700 relative"
                                    title="Pedidos Pendientes">
                                    <i class="fas fa-bell fa-lg"></i>
                                    @if(isset($pedidosPendientes))
                                        <span
                                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                            {{ $pedidosPendientes }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="{{ request()->routeIs('calendario.*') ? 'h-100 p-0' : 'p-4' }}">
                {{ $slot }}
            </main>
        </div>
    </div>
    </div>

    <script>
        function toggleTarifaDropdown() {
            const dropdown = document.getElementById('tarifaDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Cerrar dropdown al hacer click fuera
        window.addEventListener('click', function (e) {
            const btn = document.getElementById('tarifaBtn');
            const dropdown = document.getElementById('tarifaDropdown');
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>

</html>