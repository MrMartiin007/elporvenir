<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Pagos') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="container-fluid">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <!-- Tabs Header -->
                    <div class="mb-6 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="paymentTab"
                            role="tablist">
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg text-blue-600 border-blue-600"
                                    id="cheques-tab" data-tabs-target="#cheques" type="button" role="tab"
                                    aria-controls="cheques" aria-selected="true">
                                    <i class="fas fa-money-check-alt mr-2"></i>Cheques
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    id="depositos-tab" data-tabs-target="#depositos" type="button" role="tab"
                                    aria-controls="depositos" aria-selected="false">
                                    <i class="fas fa-university mr-2"></i>Depósitos
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    id="tarjetas-tab" data-tabs-target="#tarjetas" type="button" role="tab"
                                    aria-controls="tarjetas" aria-selected="false">
                                    <i class="fas fa-credit-card mr-2"></i>Tarjetas de Crédito
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Contents -->
                    <div id="paymentTabContent">

                        <!-- CHEQUES TAB -->
                        <div class="p-4 rounded-lg bg-white" id="cheques" role="tabpanel" aria-labelledby="cheques-tab">
                            <!-- Search Bar for Cheques -->
                            <div class="mb-4">
                                <form method="GET" action="{{ route('pagos.index') }}"
                                    class="d-flex justify-content-end">
                                    <div class="input-group" style="max-width: 400px;">
                                        <input type="text" name="buscar_cheque" value="{{ request('buscar_cheque') }}"
                                            class="form-control border-pink"
                                            placeholder="Buscar por no. cheque o empresa...">
                                        <button type="submit" class="btn text-white" style="background-color: #d63384;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                        <tr>
                                            <th>#</th>
                                            <th>No. Cheque</th>
                                            <th>Empresa</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Fecha Cobro</th>
                                            <th>No. Factura</th>
                                            <th>Foto Factura</th>
                                            <th>Foto Cheque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cheques as $cheque)
                                            <tr class="text-center align-middle">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $cheque->no_cheque }}</td>
                                                <td>{{ $cheque->factura->empresa->nombre_empresa ?? 'N/A' }}</td>
                                                <td>Q. {{ number_format($cheque->factura->monto ?? 0, 2) }}</td>
                                                <td>
                                                    <span class="badge 
                                                                {{ $cheque->estado == 0 ? 'bg-danger' : '' }}
                                                                {{ $cheque->estado == 1 ? 'bg-warning' : '' }}
                                                                {{ $cheque->estado == 2 ? 'bg-success' : '' }}
                                                            ">
                                                        @if($cheque->estado == 0) Anulado @endif
                                                        @if($cheque->estado == 1) Pendiente @endif
                                                        @if($cheque->estado == 2) Confirmado @endif
                                                    </span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($cheque->fecha_cobro)->format('d/m/Y') }}</td>
                                                <td>{{ $cheque->factura->numero_factura ?? 'N/A' }}</td>
                                                <td>
                                                    @if($cheque->factura && $cheque->factura->foto_fac)
                                                        <img src="{{ asset('storage/' . $cheque->factura->foto_fac) }}"
                                                            class="rounded-circle object-cover cursor-pointer"
                                                            style="width: 45px; height: 45px;" alt="Foto Factura"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $cheque->factura->foto_fac) }}')">
                                                    @else
                                                        <span class="text-muted">Sin foto</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cheque->foto_ch)
                                                        <img src="{{ asset('storage/' . $cheque->foto_ch) }}"
                                                            class="rounded-circle object-cover cursor-pointer"
                                                            style="width: 45px; height: 45px;" alt="Foto Cheque"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $cheque->foto_ch) }}')">
                                                    @else
                                                        <span class="text-muted">Sin foto</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">No hay cheques registrados
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination for Cheques -->
                            <div class="mt-4">
                                {{ $cheques->links() }}
                            </div>
                        </div>

                        <!-- DEPOSITOS TAB -->
                        <div class="hidden p-4 rounded-lg bg-white" id="depositos" role="tabpanel"
                            aria-labelledby="depositos-tab">
                            <!-- Search Bar for Depositos -->
                            <div class="mb-4">
                                <form method="GET" action="{{ route('pagos.index') }}"
                                    class="d-flex justify-content-end">
                                    <div class="input-group" style="max-width: 400px;">
                                        <input type="text" name="buscar_deposito"
                                            value="{{ request('buscar_deposito') }}" class="form-control border-pink"
                                            placeholder="Buscar por no. boleta o empresa...">
                                        <button type="submit" class="btn text-white" style="background-color: #d63384;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                        <tr>
                                            <th>#</th>
                                            <th>No. Boleta</th>
                                            <th>Empresa</th>
                                            <th>Monto</th>
                                            <th>Fecha Depósito</th>
                                            <th>No. Factura</th>
                                            <th>Foto Factura</th>
                                            <th>Foto Depósito</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($depositos as $deposito)
                                            <tr class="text-center align-middle">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $deposito->no_deposito }}</td>
                                                <td>{{ $deposito->factura->empresa->nombre_empresa ?? 'N/A' }}</td>
                                                <td>Q. {{ number_format($deposito->factura->monto ?? 0, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($deposito->fecha_deposito)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $deposito->factura->numero_factura ?? 'N/A' }}</td>
                                                <td>
                                                    @if($deposito->factura && $deposito->factura->foto_fac)
                                                        <img src="{{ asset('storage/' . $deposito->factura->foto_fac) }}"
                                                            class="rounded-circle object-cover cursor-pointer"
                                                            style="width: 45px; height: 45px;" alt="Foto Factura"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $deposito->factura->foto_fac) }}')">
                                                    @else
                                                        <span class="text-muted">Sin foto</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($deposito->foto_deposito)
                                                        <img src="{{ asset('storage/' . $deposito->foto_deposito) }}"
                                                            class="rounded-circle object-cover cursor-pointer"
                                                            style="width: 45px; height: 45px;" alt="Foto Depósito"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $deposito->foto_deposito) }}')">
                                                    @else
                                                        <span class="text-muted">Sin foto</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No hay depósitos registrados
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination for Depositos -->
                            <div class="mt-4">
                                {{ $depositos->links() }}
                            </div>
                        </div>

                        <!-- TARJETAS TAB -->
                        <div class="hidden p-4 rounded-lg bg-white" id="tarjetas" role="tabpanel"
                            aria-labelledby="tarjetas-tab">
                            <!-- Search Bar for Tarjetas -->
                            <div class="mb-4">
                                <form method="GET" action="{{ route('pagos.index') }}"
                                    class="d-flex justify-content-end">
                                    <div class="input-group" style="max-width: 400px;">
                                        <input type="text" name="buscar_tarjeta" value="{{ request('buscar_tarjeta') }}"
                                            class="form-control border-pink"
                                            placeholder="Buscar por no. autorización o empresa...">
                                        <button type="submit" class="btn text-white" style="background-color: #d63384;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="text-center" style="background-color: #fce4ec; color: #880e4f;">
                                        <tr>
                                            <th>#</th>
                                            <th>No. Autorización</th>
                                            <th>Empresa</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Fecha Pago</th>
                                            <th>No. Factura</th>
                                            <th>Foto Factura</th>
                                            <th>Foto Voucher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tarjetas as $tarjeta)
                                            <tr class="text-center align-middle">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $tarjeta->no_autorizacion }}</td>
                                                <td>{{ $tarjeta->factura->empresa->nombre_empresa ?? 'N/A' }}</td>
                                                <td>Q. {{ number_format($tarjeta->factura->monto ?? 0, 2) }}</td>
                                                <td>
                                                    <span class="badge 
                                                                {{ $tarjeta->estado == 0 ? 'bg-danger' : '' }}
                                                                {{ $tarjeta->estado == 1 ? 'bg-warning' : '' }}
                                                                {{ $tarjeta->estado == 2 ? 'bg-success' : '' }}
                                                            ">
                                                        @if($tarjeta->estado == 0) Anulado @endif
                                                        @if($tarjeta->estado == 1) Pendiente @endif
                                                        @if($tarjeta->estado == 2) Confirmado @endif
                                                    </span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($tarjeta->fecha_pago)->format('d/m/Y') }}</td>
                                                <td>{{ $tarjeta->factura->numero_factura ?? 'N/A' }}</td>
                                                <td>
                                                    @if($tarjeta->factura && $tarjeta->factura->foto_fac)
                                                        <img src="{{ asset('storage/' . $tarjeta->factura->foto_fac) }}"
                                                            class="rounded-circle object-cover cursor-pointer"
                                                            style="width: 45px; height: 45px;" alt="Foto Factura"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $tarjeta->factura->foto_fac) }}')">
                                                    @else
                                                        <span class="text-muted">Sin foto</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tarjeta->foto_tc)
                                                        <img src="{{ asset('storage/' . $tarjeta->foto_tc) }}"
                                                            class="rounded-circle object-cover cursor-pointer"
                                                            style="width: 45px; height: 45px;" alt="Foto Voucher"
                                                            data-bs-toggle="modal" data-bs-target="#fotoModal"
                                                            onclick="mostrarImagen('{{ asset('storage/' . $tarjeta->foto_tc) }}')">
                                                    @else
                                                        <span class="text-muted">Sin foto</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">No hay tarjetas registradas
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination for Tarjetas -->
                            <div class="mt-4">
                                {{ $tarjetas->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar imagen -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Foto" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarImagen(ruta) {
            document.getElementById('modalImage').src = ruta;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('[role="tab"]');
            const tabContents = document.querySelectorAll('[role="tabpanel"]');

            // 1. Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const buscarCheque = urlParams.get('buscar_cheque');
            const buscarDeposito = urlParams.get('buscar_deposito');
            const buscarTarjeta = urlParams.get('buscar_tarjeta');
            const currentTab = urlParams.get('tab');

            // 2. Determine active tab ID
            // Priority: Search Params > Tab Param > Default (Cheques)
            let activeTabId = 'cheques-tab';

            if (buscarDeposito) {
                activeTabId = 'depositos-tab';
            } else if (buscarTarjeta) {
                activeTabId = 'tarjetas-tab';
            } else if (buscarCheque) {
                activeTabId = 'cheques-tab';
            } else if (currentTab) {
                // Map logical tab names to IDs
                if (currentTab === 'depositos') activeTabId = 'depositos-tab';
                if (currentTab === 'tarjetas') activeTabId = 'tarjetas-tab';
                if (currentTab === 'cheques') activeTabId = 'cheques-tab';
            }

            // 3. Helper to reset visual state of all tabs
            const resetTabs = () => {
                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    t.classList.remove('text-blue-600', 'border-blue-600');
                    t.classList.add('border-transparent');
                });
                tabContents.forEach(content => content.classList.add('hidden'));
            };

            // 4. Function to activate a specific tab element
            const activateTab = (tabElement) => {
                if (!tabElement) return;

                resetTabs();

                // Style the tab button
                tabElement.setAttribute('aria-selected', 'true');
                tabElement.classList.remove('border-transparent');
                tabElement.classList.add('text-blue-600', 'border-blue-600');

                // Show the content
                const targetId = tabElement.dataset.tabsTarget; // e.g., #cheques
                const targetContent = document.querySelector(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            };

            // 5. Initial Activation
            const initialTab = document.getElementById(activeTabId);
            activateTab(initialTab);

            // 6. Handle Clicks - Clean URL and switch tab
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();

                    let targetTabName = 'cheques'; // Default
                    if (tab.id === 'depositos-tab') targetTabName = 'depositos';
                    if (tab.id === 'tarjetas-tab') targetTabName = 'tarjetas';

                    // Redirect to clean URL with just the tab parameter
                    // This clears any existing search parameters
                    const baseUrl = window.location.origin + window.location.pathname;
                    window.location.href = `${baseUrl}?tab=${targetTabName}`;
                });
            });
        });
    </script>
</x-app-layout>