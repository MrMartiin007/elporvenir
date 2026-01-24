<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Factura') }} #{{ $factura->numero_factura }}
        </h2>
    </x-slot>

    <div class="py-1">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">

                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg
          text-gray-700 font-medium shadow-sm
          hover:bg-gray-100 hover:text-gray-900 hover:shadow
          transition duration-200 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Regresar
                </a>


            </div>
            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">

                <!-- Status Bar -->
                <div class="p-6 
                    {{ $factura->estado == 0 ? 'bg-red-50' : '' }}
                    {{ $factura->estado == 1 ? 'bg-yellow-50' : '' }}
                    {{ $factura->estado == 2 ? 'bg-green-50' : '' }}
                    {{ $factura->estado >= 3 ? 'bg-blue-50' : '' }}
                    border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Estado Actual</span>
                        <div class="flex items-center mt-1">
                            @if($factura->estado == 0)
                                <i class="fas fa-ban text-red-500 mr-2 text-xl"></i> <span
                                    class="text-xl font-bold text-red-700">ANULADA</span>
                            @elseif($factura->estado == 1)
                                <i class="fas fa-clock text-yellow-500 mr-2 text-xl"></i> <span
                                    class="text-xl font-bold text-yellow-700">PENDIENTE DE PAGO</span>
                            @elseif($factura->estado == 2)
                                <i class="fas fa-money-bill-wave text-green-500 mr-2 text-xl"></i> <span
                                    class="text-xl font-bold text-green-700">PAGADA (EFECTIVO)</span>
                            @elseif($factura->estado == 3)
                                <i class="fas fa-money-check-alt text-blue-500 mr-2 text-xl"></i> <span
                                    class="text-xl font-bold text-blue-700">PAGADA (CHEQUE)</span>
                            @elseif($factura->estado == 4)
                                <i class="fas fa-credit-card text-purple-500 mr-2 text-xl"></i> <span
                                    class="text-xl font-bold text-purple-700">PAGADA (TARJETA)</span>
                            @elseif($factura->estado == 5)
                                <i class="fas fa-university text-teal-500 mr-2 text-xl"></i> <span
                                    class="text-xl font-bold text-teal-700">PAGADA (DEPÓSITO)</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Monto Total</span>
                        <div class="text-3xl font-extrabold text-gray-800">Q. {{ number_format($factura->monto, 2) }}
                        </div>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Left Column: Invoice Details -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Información de la Factura</h3>

                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Empresa / Proveedor</p>
                                    <p class="font-bold text-gray-800">{{ $factura->empresa->nombre_empresa ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Fecha de Registro</p>
                                    <p class="font-bold text-gray-800">{{ $factura->created_at->format('d/m/Y h:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Número de Factura</p>
                                    <p class="font-bold text-gray-800">{{ $factura->numero_factura }}</p>
                                </div>
                            </div>
                        </div>

                        <h4 class="font-semibold text-gray-700 mb-2">Foto de la Factura</h4>
                        @if($factura->foto_fac)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $factura->foto_fac) }}" alt="Foto Factura"
                                    class="h-20 w-auto object-cover rounded cursor-pointer border hover:opacity-75"
                                    data-bs-toggle="modal" data-bs-target="#imageModal" onclick="showImage(this.src)">
                            </div>
                        @else
                            <div
                                class="bg-gray-100 rounded-lg p-8 text-center text-gray-400 h-32 w-full max-w-xs mx-auto flex flex-col items-center justify-center">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p>Sin foto disponible</p>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Payment Details -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Historial de Pagos</h3>

                        @if($factura->cheques->count() > 0 || $factura->tarjetasCredito->count() > 0 || $factura->deposito)

                            <!-- Cheques History -->
                            @foreach($factura->cheques as $cheque)
                                <div
                                    class="rounded-lg p-4 mb-4 border {{ $cheque->estado == 0 ? 'bg-red-50 border-red-200' : ($cheque->estado == 2 ? 'bg-green-50 border-green-200' : 'bg-blue-50 border-blue-200') }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4
                                            class="font-bold {{ $cheque->estado == 0 ? 'text-red-800' : ($cheque->estado == 2 ? 'text-green-800' : 'text-blue-800') }}">
                                            <i class="fas fa-money-check-alt mr-2"></i> Cheque
                                        </h4>
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold {{ $cheque->estado == 0 ? 'bg-red-200 text-red-800' : ($cheque->estado == 2 ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800') }}">
                                            {{ $cheque->estado == 0 ? 'ANULADO' : ($cheque->estado == 2 ? 'CONFIRMADO' : 'PENDIENTE') }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="text-gray-500">No. Cheque:</span> <span
                                                class="font-semibold">{{ $cheque->no_cheque }}</span></div>
                                        <div><span class="text-gray-500">Fecha:</span> <span
                                                class="font-semibold">{{ \Carbon\Carbon::parse($cheque->fecha_cobro)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    @if($cheque->foto_ch)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-400 mb-1">Comprobante:</p>
                                            <img src="{{ asset('storage/' . $cheque->foto_ch) }}" alt="Foto Cheque"
                                                class="h-20 w-auto object-cover rounded cursor-pointer border hover:opacity-75"
                                                onclick="showImage(this.src)" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Tarjetas History -->
                            @foreach($factura->tarjetasCredito as $tarjeta)
                                <div
                                    class="rounded-lg p-4 mb-4 border {{ $tarjeta->estado == 0 ? 'bg-red-50 border-red-200' : ($tarjeta->estado == 2 ? 'bg-green-50 border-green-200' : 'bg-purple-50 border-purple-200') }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4
                                            class="font-bold {{ $tarjeta->estado == 0 ? 'text-red-800' : ($tarjeta->estado == 2 ? 'text-green-800' : 'text-purple-800') }}">
                                            <i class="fas fa-credit-card mr-2"></i> Tarjeta
                                        </h4>
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold {{ $tarjeta->estado == 0 ? 'bg-red-200 text-red-800' : ($tarjeta->estado == 2 ? 'bg-green-200 text-green-800' : 'bg-purple-200 text-purple-800') }}">
                                            {{ $tarjeta->estado == 0 ? 'ANULADA' : ($tarjeta->estado == 2 ? 'CONFIRMADA' : 'PENDIENTE') }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="text-gray-500">Autorización:</span> <span
                                                class="font-semibold">{{ $tarjeta->no_autorizacion }}</span></div>
                                        <div><span class="text-gray-500">Fecha:</span> <span
                                                class="font-semibold">{{ \Carbon\Carbon::parse($tarjeta->fecha_pago)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    @if($tarjeta->foto_tc)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-400 mb-1">Comprobante:</p>
                                            <img src="{{ asset('storage/' . $tarjeta->foto_tc) }}" alt="Foto Voucher"
                                                class="h-20 w-auto object-cover rounded cursor-pointer border hover:opacity-75"
                                                onclick="showImage(this.src)" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Deposito (Assuming simple hasOne/active for now, logic preserved) -->
                            @if($factura->deposito)
                                <div class="bg-teal-50 rounded-lg p-4 mb-4 border border-teal-100">
                                    <h4 class="font-bold text-teal-800 mb-2"><i class="fas fa-university mr-2"></i> Depósito
                                    </h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div><span class="text-gray-500">Boleta:</span> <span
                                                class="font-semibold">{{ $factura->deposito->no_deposito }}</span></div>
                                        <div><span class="text-gray-500">Fecha:</span> <span
                                                class="font-semibold">{{ \Carbon\Carbon::parse($factura->deposito->fecha_deposito)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    @if($factura->deposito->foto_deposito)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-400 mb-1">Comprobante:</p>
                                            <img src="{{ asset('storage/' . $factura->deposito->foto_deposito) }}" alt="Foto Voucher"
                                                class="h-20 w-auto object-cover rounded cursor-pointer border hover:opacity-75"
                                                onclick="showImage(this.src)" data-bs-toggle="modal" data-bs-target="#imageModal">
                                        </div>
                                    @endif
                                </div>
                            @endif

                        @else
                            <div class="bg-gray-100 p-4 rounded text-center text-gray-500">
                                No hay historial de pagos registrado.
                            </div>
                        @endif

                        @if($factura->estado == 1)
                            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Esta factura está pendiente de pago.
                                            <a href="{{ route('facturas.liquidar', $factura->id) }}"
                                                class="font-bold underline cursor-pointer hover:text-yellow-800">
                                                Ir a Liquidar
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <button type="button" class="btn-close absolute top-2 right-2 z-10 bg-white p-2 rounded-full shadow"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <img id="modalFullImage" src="" alt="Full size" class="w-full h-auto rounded">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalFullImage').src = src;
        }
    </script>
</x-app-layout>