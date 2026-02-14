<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pedido #{{ $pedido->numero_pedido }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Columna Izquierda: Detalles del Pedido --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Productos --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Productos</h3>
                                @if(in_array($pedido->estado, ['pendiente', 'confirmado']))
                                    <button type="button"
                                        class="px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
                                        data-bs-toggle="modal" data-bs-target="#searchProductModal">
                                        <i class="fas fa-plus mr-1"></i> Agregar Producto
                                    </button>
                                @endif
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Foto</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Código</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Producto</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Precio</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Desc.</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Cant.</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Subtotal</th>
                                            <th
                                                class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">
                                                Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pedido->detalles as $detalle)
                                            <tr>
                                                <td class="px-4 py-3 text-center">
                                                    @if($detalle->producto && $detalle->producto->foto_producto)
                                                        <img src="{{ asset('storage/' . $detalle->producto->foto_producto) }}"
                                                            alt="Foto"
                                                            class="h-10 w-10 rounded-full object-cover mx-auto border shadow-sm cursor-pointer"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $detalle->producto->foto_producto) }}')">
                                                    @else
                                                        <span class="text-xs text-gray-400">Sin foto</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-500">
                                                    {{ $detalle->producto->codigo_producto ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ $detalle->producto->detalle_producto ?? 'Producto Eliminado' }}
                                                    @if(!empty($detalle->producto) && $detalle->producto->stock <= 0)
                                                        <span class="text-red-500 text-xs ml-2">(Sin Stock)</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500 text-right">
                                                    @if($detalle->descuento > 0 && $detalle->descuento < $detalle->precio_unitario)
                                                        <div class="flex flex-col items-end">
                                                            <span class="text-xs text-gray-400 line-through">Q. {{ number_format($detalle->precio_unitario, 2) }}</span>
                                                            <span class="font-bold text-gray-900">Q. {{ number_format($detalle->precio_unitario - $detalle->descuento, 2) }}</span>
                                                        </div>
                                                    @else
                                                        Q. {{ number_format($detalle->precio_unitario, 2) }}
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if(in_array($pedido->estado, ['pendiente', 'confirmado']))
                                                        <input type="number" name="descuento" form="update-form-{{ $detalle->id }}"
                                                            value="{{ $detalle->descuento }}" step="0.01" min="0" max="{{ $detalle->precio_unitario }}"
                                                            class="w-20 h-8 text-center text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                            placeholder="0.00">
                                                    @else
                                                        @if($detalle->descuento > 0)
                                                            <span class="text-red-600 font-medium">- Q. {{ number_format($detalle->descuento, 2) }}</span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500 text-center"
                                                    style="min-width: 120px;">
                                                    @if(in_array($pedido->estado, ['pendiente', 'confirmado']))
                                                        <form id="update-form-{{ $detalle->id }}"
                                                            action="{{ route('pedidos.detalles.update', [$pedido->id, $detalle->id]) }}"
                                                            method="POST" class="flex justify-center items-center">
                                                            @csrf @method('PATCH')
                                                            <input type="number" name="cantidad"
                                                                value="{{ $detalle->cantidad }}" min="1"
                                                                class="w-16 h-8 text-center text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                            <button type="submit" class="ml-2 text-blue-600 hover:text-blue-900"
                                                                title="Actualizar Cantidad">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-900 font-medium">{{ $detalle->cantidad }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">
                                                    Q. {{ number_format($detalle->subtotal, 2) }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if(in_array($pedido->estado, ['pendiente', 'confirmado']))
                                                        <form id="delete-form-{{ $detalle->id }}"
                                                            action="{{ route('pedidos.detalles.destroy', [$pedido->id, $detalle->id]) }}"
                                                            method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDelete('delete-form-{{ $detalle->id }}')"
                                                                class="text-red-600 hover:text-red-900"
                                                                title="Eliminar Producto">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400" title="No editable"><i
                                                                class="fas fa-lock"></i></span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-2 text-right text-sm font-medium text-gray-500">Subtotal:
                                            </td>
                                            <td
                                                class="px-4 py-2 text-right text-sm font-medium text-gray-900 whitespace-nowrap">
                                                Q. {{ number_format($pedido->subtotal, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-2 text-right text-sm font-medium text-gray-500">Envío:
                                            </td>
                                            <td
                                                class="px-4 py-2 text-right text-sm font-medium text-gray-900 whitespace-nowrap">
                                                Q. {{ number_format($pedido->envio, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"
                                                class="px-4 py-2 text-right text-base font-bold text-gray-900">Total:
                                            </td>
                                            <td
                                                class="px-4 py-2 text-right text-base font-bold text-gray-900 whitespace-nowrap">
                                                Q. {{ number_format($pedido->total, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Columna Derecha: Info Cliente y Acciones --}}
                <div class="space-y-6">

                    {{-- Estado y Acciones (Botón Volver movido aquí) --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">

                            {{-- Botón Volver --}}
                            <a href="{{ route('pedidos.index') }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4">
                                &larr; Volver al Listado
                            </a>
                            <hr class="mb-4">

                            <h3 class="text-lg font-medium text-gray-900 mb-4">Estado del Pedido</h3>

                            <div class="mb-4 text-center">
                                @php
                                    $colores = [
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'confirmado' => 'bg-blue-100 text-blue-800',
                                        'enviado' => 'bg-purple-100 text-purple-800',
                                        'entregado' => 'bg-green-100 text-green-800',
                                        'cancelado' => 'bg-red-100 text-red-800',
                                    ];
                                    $clase = $colores[$pedido->estado] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span
                                    class="px-4 py-1 inline-flex text-lg leading-5 font-bold rounded-full {{ $clase }}">
                                    {{ ucfirst($pedido->estado) }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <hr class="my-4">

                                {{-- Botones de Acción según Estado --}}

                                @if($pedido->estado == 'pendiente')
                                    <form id="confirm-form-pending"
                                        action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="confirmado">
                                        <button type="button"
                                            onclick="confirmAction('confirm-form-pending', '¿Confirmar Pedido?', 'Los productos serán reservados.', 'question', 'Sí, confirmar')"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-check mr-2"></i> Confirmar Pedido
                                        </button>
                                    </form>

                                    <form id="cancel-form-pending" action="{{ route('pedidos.updateStatus', $pedido->id) }}"
                                        method="POST" class="mt-2">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="cancelado">
                                        <button type="button" onclick="confirmCancel('cancel-form-pending')"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-times mr-2"></i> Cancelar
                                        </button>
                                    </form>
                                @endif

                                @if($pedido->estado == 'confirmado')
                                    {{-- Botón para abrir modal de envío (Color Morado) --}}
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#shippingModal"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-shipping-fast mr-2"></i> Marcar Enviado
                                    </button>

                                    <form id="cancel-form-confirmed"
                                        action="{{ route('pedidos.updateStatus', $pedido->id) }}" method="POST"
                                        class="mt-2">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="cancelado">
                                        <button type="button" onclick="confirmCancel('cancel-form-confirmed')"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-undo mr-2"></i> Cancelar
                                        </button>
                                    </form>
                                @endif

                                @if($pedido->estado == 'enviado')
                                    <form id="deliver-form" action="{{ route('pedidos.updateStatus', $pedido->id) }}"
                                        method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="entregado">
                                        <button type="button"
                                            onclick="confirmAction('deliver-form', '¿Marcar como Entregado?', 'El pedido se marcará como completado finalizando el proceso.', 'success', 'Sí, entregar')"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-900 focus:outline-none focus:border-teal-900 focus:ring ring-teal-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-check-double mr-2"></i> Marcar Entregado
                                        </button>
                                    </form>
                                @endif

                                @if($pedido->estado == 'cancelado')
                                    <div class="text-center text-gray-500 py-2 bg-gray-50 rounded">
                                        Pedido Cancelado
                                    </div>
                                @endif

                                {{-- Datos de Envío Integrados --}}
                                @if($pedido->datosEnvio)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h4 class="text-sm font-bold text-gray-700 mb-2">Datos de Envío</h4>
                                        <div class="bg-blue-50 p-3 rounded-md border border-blue-100">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-xs text-gray-500 uppercase font-semibold">Guía /
                                                    Rastreo</span>
                                                <span
                                                    class="font-mono text-gray-900 font-bold ml-2">{{ $pedido->datosEnvio->numero_guia }}</span>
                                            </div>
                                            <button type="button"
                                                onclick="verComprobante('{{ asset('storage/' . $pedido->datosEnvio->comprobante) }}', '{{ $pedido->datosEnvio->tipo_comprobante }}')"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                <i class="fas fa-eye mr-2"></i> Ver Comprobante
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Información del Cliente --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Datos del Cliente</h3>

                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="block font-medium text-gray-500">Nombre:</span>
                                    <span class="text-gray-900">{{ $pedido->nombre_cliente }}</span>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-500">Teléfono:</span>
                                    <a href="https://wa.me/502{{ str_replace(['-', ' '], '', $pedido->telefono_cliente) }}"
                                        target="_blank" class="text-green-600 hover:text-green-800 font-bold">
                                        <i class="fab fa-whatsapp"></i> {{ $pedido->telefono_cliente }}
                                    </a>
                                </div>
                                @if($pedido->email_cliente)
                                    <div>
                                        <span class="block font-medium text-gray-500">Email:</span>
                                        <span class="text-gray-900">{{ $pedido->email_cliente }}</span>
                                    </div>
                                @endif
                                <hr>
                                <div>
                                    <span class="block font-medium text-gray-500">Departamento:</span>
                                    <span class="text-gray-900">{{ $pedido->departamento->nombre ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-500">Municipio:</span>
                                    <span class="text-gray-900">{{ $pedido->municipio->nombre ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block font-medium text-gray-500">Dirección:</span>
                                    <span class="text-gray-900">{{ $pedido->direccion_cliente }}</span>
                                </div>

                                {{-- Notas del Cliente (Movido aquí) --}}
                                @if($pedido->notas_cliente)
                                    <hr class="mt-4">
                                    <div class="pt-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-2">Notas:</h4>
                                        <p class="text-gray-600 bg-yellow-50 p-3 rounded border border-yellow-100 text-xs">
                                            {{ $pedido->notas_cliente }}
                                        </p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal para mostrar imagen (Igual que en productos/index) -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center bg-transparent">
                    <img id="modalImage" src="" alt="Foto del producto" class="img-fluid rounded shadow-lg"
                        style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Buscar Producto -->
    <div class="modal fade" id="searchProductModal" tabindex="-1" aria-labelledby="searchProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchProductModalLabel">Buscar y Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="searchTerm" class="form-control"
                            placeholder="Buscar por código, nombre o marca..." autocomplete="off">
                    </div>
                    <div id="searchResults" class="overflow-y-auto" style="max-height: 400px;">
                        <p class="text-center text-gray-500 py-4">Ingresa un término para buscar...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Marcar Enviado (Rediseñado) -->
    <div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-lg overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-blue-600 to-blue-800 text-white border-0">
                    <h5 class="modal-title flex items-center gap-2 font-bold text-lg" id="shippingModalLabel">
                        <i class="fas fa-shipping-fast"></i> Registrar Envío
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('pedidos.enviar', $pedido->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-6 bg-gray-50">
                        <!-- Input Guía -->
                        <div class="mb-5">
                            <label for="numero_guia" class="block text-sm font-medium text-gray-700 mb-1">Número de Guía
                                / Rastreo <span class="text-red-500">*</span></label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-barcode text-gray-400"></i>
                                </div>
                                <input type="text" name="numero_guia" id="numero_guia"
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2"
                                    placeholder="Ej: GUIA-123456789" required>
                            </div>
                        </div>

                        <!-- Input Comprobante (Drag & Drop like) -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comprobante de Envío <span
                                    class="text-red-500">*</span></label>
                            <div
                                class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-white hover:bg-gray-50 transition-colors relative">
                                <div class="space-y-1 text-center">
                                    <div id="uploadIcon">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <div id="previewContainer" class="hidden mb-2">
                                        <img id="filePreview" src="#" alt="Vista previa"
                                            class="mx-auto h-32 object-contain rounded-md shadow-sm hidden" />
                                        <div id="pdfPreview" class="hidden text-gray-500">
                                            <i class="fas fa-file-pdf text-red-500 text-4xl mb-2"></i>
                                            <p id="fileName" class="text-sm"></p>
                                        </div>
                                    </div>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="comprobante"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Subir un archivo</span>
                                            <input id="comprobante" name="comprobante" type="file" class="sr-only"
                                                accept="image/*,.pdf" required onchange="previewFile()">
                                        </label>
                                        <p class="pl-1">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF hasta 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-gray-100 border-t border-gray-200">
                        <button type="button"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-400 transition"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 shadow-md transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Confirmar Envío
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Comprobante -->
    <div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content h-[85vh]">
                <div class="modal-header bg-gray-800 text-white">
                    <h5 class="modal-title" id="proofModalLabel">Comprobante de Envío</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 flex justify-center items-center bg-gray-100 h-full">
                    <img id="proofImage" src="" alt="Comprobante"
                        class="max-h-full max-w-full object-contain hidden shadow-lg">
                    <iframe id="proofPdf" src="" class="w-full h-full hidden" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración de SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Notificaciones Flash de Sesión
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
            });
        @endif

            // Confirmación para Eliminar Detalle
            function confirmDelete(formId) {
                Swal.fire({
                    title: '¿Eliminar producto?',
                    text: "Se devolverá el stock si el pedido ha sido confirmado.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                })
            }

        // Confirmación para Cancelar Pedido
        function confirmCancel(formId) {
            Swal.fire({
                title: '¿Cancelar Pedido?',
                text: "Esta acción repondrá el stock de los productos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar pedido',
                cancelButtonText: 'No, mantener'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }

        // Confirmación Genérica para acciones de estado
        function confirmAction(formId, title, text, icon, confirmButtonText) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon, // 'warning', 'info', 'success', 'question'
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            })
        }

        function mostrarImagen(ruta) {
            document.getElementById('modalImage').src = ruta;
        }

        // Búsqueda de productos
        const searchInput = document.getElementById('searchTerm');
        const resultsContainer = document.getElementById('searchResults');
        let timeoutId;

        function performSearch(term) {
            if (term.length < 1) {
                resultsContainer.innerHTML = '<p class="text-center text-gray-500 py-4">Ingresa un término para buscar...</p>';
                return;
            }

            // Mostrar spinner o texto de carga
            resultsContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`{{ route('pedidos.productos.buscar') }}?term=${encodeURIComponent(term)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<p class="text-center text-gray-500 py-4">No se encontraron productos.</p>';
                        return;
                    }

                    let html = '<table class="min-w-full divide-y divide-gray-200"><tbody class="bg-white divide-y divide-gray-200">';
                    data.forEach(product => {
                        html += `
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    ${product.imagen ? `<img src="${product.imagen}" class="h-10 w-10 rounded-full object-cover">` : '<span class="text-xs text-gray-400">Sin foto</span>'}
                                </td>
                                <td class="px-4 py-2">
                                    <div class="text-sm font-medium text-gray-900">${product.nombre}</div>
                                    <div class="text-sm text-gray-500">${product.codigo}</div>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    Stock: ${product.stock}
                                </td>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                    Q. ${parseFloat(product.precio).toFixed(2)}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <form action="{{ route('pedidos.detalles.store', $pedido->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="${product.id}">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs uppercase font-semibold">
                                            Agregar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                    html += '</tbody></table>';
                    resultsContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsContainer.innerHTML = '<p class="text-center text-red-500 py-4">Error al buscar. Intenta de nuevo.</p>';
                });
        }

        searchInput.addEventListener('keyup', function (e) {
            clearTimeout(timeoutId);
            const term = this.value;

            if (e.key === 'Enter') {
                performSearch(term);
                return;
            }

            timeoutId = setTimeout(() => {
                performSearch(term);
            }, 300);
        });

        // Enflocar input al abrir modal
        const searchModal = document.getElementById('searchProductModal');
        searchModal.addEventListener('shown.bs.modal', function () {
            searchInput.focus();
        });

        // Vista previa del archivo
        function previewFile() {
            const preview = document.getElementById('filePreview');
            const pdfPreview = document.getElementById('pdfPreview');
            const fileName = document.getElementById('fileName');
            const file = document.getElementById('comprobante').files[0];
            const reader = new FileReader();
            const uploadIcon = document.getElementById('uploadIcon');
            const previewContainer = document.getElementById('previewContainer');

            reader.onloadend = function () {
                uploadIcon.classList.add('hidden');
                previewContainer.classList.remove('hidden');

                if (file.type.match('image.*')) {
                    preview.src = reader.result;
                    preview.classList.remove('hidden');
                    pdfPreview.classList.add('hidden');
                } else if (file.type === 'application/pdf') {
                    preview.classList.add('hidden');
                    pdfPreview.classList.remove('hidden');
                    fileName.textContent = file.name;
                } else {
                    // Fallback for other types
                    preview.classList.add('hidden');
                    pdfPreview.classList.add('hidden');
                }
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                uploadIcon.classList.remove('hidden');
                previewContainer.classList.add('hidden');
            }
        }

        // Ver Comprobante en Modal
        function verComprobante(url, type) {
            const modal = new bootstrap.Modal(document.getElementById('proofModal'));
            const imageEl = document.getElementById('proofImage');
            const pdfEl = document.getElementById('proofPdf');

            if (type === 'pdf') {
                imageEl.classList.add('hidden');
                pdfEl.src = url;
                pdfEl.classList.remove('hidden');
            } else {
                pdfEl.classList.add('hidden');
                imageEl.src = url;
                imageEl.classList.remove('hidden');
            }

            modal.show();
        }
    </script>
</x-app-layout>