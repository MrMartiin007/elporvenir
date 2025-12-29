<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Liquidar Factura') }} #{{ $factura->numero_factura }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900">

                    <!-- Tabs Header (Classic Style) -->
                    <div class="mb-6 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="paymentTab"
                            role="tablist">
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 border-transparent"
                                    id="efectivo-tab" data-tabs-target="#efectivo" type="button" role="tab"
                                    aria-controls="efectivo" aria-selected="false">
                                    <i class="fas fa-money-bill-wave mr-2"></i>Efectivo
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="cheque-tab" data-tabs-target="#cheque" type="button" role="tab"
                                    aria-controls="cheque" aria-selected="false">
                                    <i class="fas fa-money-check-alt mr-2"></i>Cheque
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="tarjeta-tab" data-tabs-target="#tarjeta" type="button" role="tab"
                                    aria-controls="tarjeta" aria-selected="false">
                                    <i class="fas fa-credit-card mr-2"></i>Tarjeta de Crédito
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                    id="deposito-tab" data-tabs-target="#deposito" type="button" role="tab"
                                    aria-controls="deposito" aria-selected="false">
                                    <i class="fas fa-university mr-2"></i>Depósito
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div id="paymentTabContent" class="max-w-4xl mx-auto">

                        <!-- EFECTIVO -->
                        <div class="hidden p-6 rounded-lg bg-gray-50" id="efectivo" role="tabpanel"
                            aria-labelledby="efectivo-tab">
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-800 mb-4">Pago en Efectivo</h3>
                                <p class="text-gray-600 mb-6">Confirmar recepción de <strong>Q.
                                        {{ number_format($factura->monto, 2) }}</strong></p>

                                <form action="{{ route('facturas.pagar_efectivo', $factura->id) }}" method="POST"
                                    class="pagar-form">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow transition duration-150">
                                        <i class="fas fa-check mr-2"></i> Confirmar Pago
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- CHEQUE -->
                        <div class="hidden p-6 rounded-lg bg-gray-50" id="cheque" role="tabpanel" aria-labelledby="cheque-tab">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-800">Datos del Cheque</h3>
                                <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded">Total a Pagar: Q. {{ number_format($factura->monto, 2) }}</span>
                            </div>
                            <form action="{{ route('facturas.pagar_cheque') }}" method="POST"
                                enctype="multipart/form-data" class="pagar-form">
                                @csrf
                                <input type="hidden" name="facturas_id" value="{{ $factura->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <x-input-label for="no_cheque" :value="__('Número de Cheque')" />
                                        <x-text-input id="no_cheque" name="no_cheque" type="text"
                                            class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="fecha_cobro" :value="__('Fecha de Cobro')" />
                                        <x-text-input id="fecha_cobro" name="fecha_cobro" type="date"
                                            class="mt-1 block w-full" required />
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <x-input-label :value="__('Foto del Cheque')" class="mb-2" />
                                    @include('components.drag-drop-upload', ['id' => 'foto_ch', 'name' => 'foto_ch'])
                                </div>

                                <div class="text-right">
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow transition duration-150">
                                        Registrar Cheque
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TARJETA -->
                        <div class="hidden p-6 rounded-lg bg-gray-50" id="tarjeta" role="tabpanel" aria-labelledby="tarjeta-tab">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-800">Datos de la Transacción</h3>
                                <span class="bg-purple-100 text-purple-800 text-sm font-semibold px-3 py-1 rounded">Total a Pagar: Q. {{ number_format($factura->monto, 2) }}</span>
                            </div>
                            <form action="{{ route('facturas.pagar_tarjeta') }}" method="POST"
                                enctype="multipart/form-data" class="pagar-form">
                                @csrf
                                <input type="hidden" name="facturas_id" value="{{ $factura->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <x-input-label for="no_autorizacion" :value="__('No. Autorización')" />
                                        <x-text-input id="no_autorizacion" name="no_autorizacion" type="text"
                                            class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="fecha_pago" :value="__('Fecha de Pago')" />
                                        <x-text-input id="fecha_pago" name="fecha_pago" type="date"
                                            class="mt-1 block w-full" required />
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <x-input-label :value="__('Foto del Voucher')" class="mb-2" />
                                    @include('components.drag-drop-upload', ['id' => 'foto_tc', 'name' => 'foto_tc'])
                                </div>

                                <div class="text-right">
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow transition duration-150">
                                        Registrar Pago
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- DEPOSITO -->
                        <div class="hidden p-6 rounded-lg bg-gray-50" id="deposito" role="tabpanel" aria-labelledby="deposito-tab">
                           <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-800">Datos del Depósito</h3>
                                <span class="bg-teal-100 text-teal-800 text-sm font-semibold px-3 py-1 rounded">Total a Pagar: Q. {{ number_format($factura->monto, 2) }}</span>
                            </div>
                            <form action="{{ route('facturas.pagar_deposito') }}" method="POST"
                                enctype="multipart/form-data" class="pagar-form">
                                @csrf
                                <input type="hidden" name="facturas_id" value="{{ $factura->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <x-input-label for="no_deposito" :value="__('No. Boleta')" />
                                        <x-text-input id="no_deposito" name="no_deposito" type="text"
                                            class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="fecha_deposito" :value="__('Fecha de Depósito')" />
                                        <x-text-input id="fecha_deposito" name="fecha_deposito" type="date"
                                            class="mt-1 block w-full" required />
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <x-input-label :value="__('Foto de la Boleta')" class="mb-2" />
                                    @include('components.drag-drop-upload', ['id' => 'foto_deposito', 'name' => 'foto_deposito'])
                                </div>

                                <div class="text-right">
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow transition duration-150">
                                        Registrar Depósito
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-100 flex justify-center">
                        <a href="{{ route('facturas.index') }}"
                            class="text-gray-500 hover:text-gray-900 transition underline">Cancelar y volver al
                            listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('[role="tab"]');
            const tabContents = document.querySelectorAll('[role="tabpanel"]');

            // Reset tabs function
            const resetTabs = () => {
                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    // Reset to default inactive style
                    t.classList.remove('text-blue-600', 'border-blue-600', 'active', 'border-b-2');
                    t.classList.add('border-transparent');
                });
            };

            // Init: Activate first tab (Efectivo) by default or none
            // Let's activate Efectivo by default
            const firstTab = document.getElementById('efectivo-tab');
            if (firstTab) {
                firstTab.click(); // Trigger click to set initial state
                firstTab.classList.add('text-blue-600', 'border-blue-600', 'border-b-2');
                firstTab.classList.remove('border-transparent');
                document.getElementById('efectivo').classList.remove('hidden');
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    resetTabs();

                    // Activate clicked tab
                    tab.setAttribute('aria-selected', 'true');
                    tab.classList.remove('border-transparent');
                    tab.classList.add('text-blue-600', 'border-blue-600', 'border-b-2');

                    // Show content
                    tabContents.forEach(content => content.classList.add('hidden'));
                    const target = document.querySelector(tab.dataset.tabsTarget);
                    target.classList.remove('hidden');
                });
            });

            // Initialize Drag & Drop functionality (Delegated to component logic if possible, but here we init the scripts)
            document.querySelectorAll('.drop-area').forEach(dropArea => {
                const inputId = dropArea.dataset.inputId;
                const fileInput = document.getElementById(inputId);
                const dropContent = document.getElementById('drop-content-' + inputId);
                const previewContainer = document.getElementById('preview-container-' + inputId);
                const previewImg = document.getElementById('preview-' + inputId);
                const filenameText = document.getElementById('filename-' + inputId);
                const removeBtn = document.getElementById('remove-' + inputId);

                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Highlight drop area when item is dragged over it
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, () => {
                        dropArea.classList.add('border-indigo-500', 'bg-indigo-50');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, () => {
                        dropArea.classList.remove('border-indigo-500', 'bg-indigo-50');
                    }, false);
                });

                // Handle dropped files
                dropArea.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    handleFiles(files);
                }, false);

                dropArea.addEventListener('click', () => {
                    fileInput.click();
                });

                fileInput.addEventListener('change', function () {
                    handleFiles(this.files);
                });

                function handleFiles(files) {
                    if (files.length > 0) {
                        const file = files[0];
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                previewImg.src = e.target.result;
                                dropContent.classList.add('hidden');
                                previewContainer.classList.remove('hidden');
                                filenameText.textContent = file.name;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            alert("Solo se permiten imágenes.");
                        }
                    }
                }

                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation(); // Stop click from bubbling to dropArea
                    fileInput.value = '';
                    previewImg.src = '#';
                    dropContent.classList.remove('hidden');
                    previewContainer.classList.add('hidden');
                    filenameText.textContent = '';
                });
            });

            // SweetAlert Confirmations
            document.querySelectorAll('.pagar-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Confirmar Pago',
                        text: "¿Está seguro de registrar este pago?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, registrar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Procesando...',
                                text: 'Por favor espere',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                    form.submit();
                                }
                            });
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>