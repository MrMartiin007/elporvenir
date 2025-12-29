<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Factura') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('facturas.update', $factura->id) }}" method="POST"
                        enctype="multipart/form-data" class="guardar">
                        @csrf
                        @method('PUT')
                        <div class="max-w-4xl w-full mx-auto sm:px-4 lg:px-6">

                            <!-- Foto del producto - Drag & Drop -->
                            <div class="mb-6">
                                <x-input-label for="foto_fac" :value="__('Foto de la Factura')" />

                                <div id="drop-area"
                                    class="mt-2 border-2 border-dashed border-gray-300 rounded-lg p-6 
                                    text-center cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out relative">

                                    <div id="drop-content" class="{{ $factura->foto_fac ? 'hidden' : '' }}">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">Arrastra y suelta una nueva foto aquí</p>
                                        <p class="mt-1 text-xs text-gray-500">o haz clic para seleccionar</p>
                                    </div>

                                    <div id="preview-container" class="{{ $factura->foto_fac ? '' : 'hidden' }}">
                                        @if($factura->foto_fac)
                                            <img id="foto_preview" src="{{ asset('storage/' . $factura->foto_fac) }}"
                                                alt="Vista previa" class="mx-auto max-w-full h-auto rounded-lg"
                                                style="max-height: 300px;">
                                            <p id="file-name" class="mt-1 text-sm text-gray-700 text-center">Foto Actual</p>
                                        @else
                                            <img id="foto_preview" src="#" alt="Vista previa"
                                                class="mx-auto max-w-full h-auto rounded-lg" style="max-height: 300px;">
                                            <p id="file-name" class="mt-1 text-sm text-gray-700 text-center"></p>
                                        @endif

                                        <button type="button" onclick="removePreview(event)"
                                            class="mt-2 text-xs text-red-500 hover:text-red-700 font-bold uppercase tracking-wider">
                                            Cambiar imagen
                                        </button>
                                    </div>

                                    <input type="file" id="foto_fac" name="foto_fac" class="hidden" accept="image/*" />
                                </div>
                                <x-input-error :messages="$errors->get('foto_fac')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="mb-4">
                                    <x-input-label for="numero_factura" :value="__('Número de Factura')" />
                                    <x-text-input id="numero_factura" name="numero_factura" type="text"
                                        class="mt-1 block w-full" :value="old('numero_factura', $factura->numero_factura)" required />
                                    <x-input-error :messages="$errors->get('numero_factura')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="monto" :value="__('Monto (Q)')" />
                                    <x-text-input id="monto" name="monto" type="number" step="0.01"
                                        class="mt-1 block w-full" :value="old('monto', $factura->monto)" required />
                                    <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="empresas_id" :value="__('Empresa / Proveedor')" />
                                    <select id="empresas_id" name="empresas_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}" {{ old('empresas_id', $factura->empresas_id) == $empresa->id ? 'selected' : '' }}>
                                                {{ $empresa->nombre_empresa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('empresas_id')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="estado" :value="__('Estado')" />
                                    <select id="estado" name="estado"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                        <option value="Pendiente" {{ old('estado', $factura->estado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="Pagada" {{ old('estado', $factura->estado) == 'Pagada' ? 'selected' : '' }}>Pagada</option>
                                        <option value="Anulada" {{ old('estado', $factura->estado) == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 mt-6">
                                <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Actualizar Factura
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#empresas_id').select2({
                placeholder: "Seleccione una empresa",
                width: '100%',
                dropdownAutoWidth: true,
            });

            // Select2 Custom Styling for Tailwind Compatibility
            $('.select2-selection').css({
                'height': '2.5rem',
                'padding': '0.5rem 0.75rem',
                'font-size': '1rem',
                'border-radius': '0.375rem',
                'border': '1px solid #d1d5db',
                'display': 'flex',
                'align-items': 'center',
                'background-color': '#fff'
            });

            $('.select2-selection__rendered').css({
                'line-height': 'normal',
                'font-size': '1rem',
                'color': '#111827'
            });

            $('.select2-selection__arrow').css({
                'height': '100%',
                'display': 'flex',
                'align-items': 'center'
            });
        });
    </script>
    <script>
        // Drag & drop logic
        const dropArea = document.getElementById('drop-area');
        const dropContent = document.getElementById('drop-content');
        const previewContainer = document.getElementById('preview-container');
        const fileInput = document.getElementById('foto_fac');
        const preview = document.getElementById('foto_preview');
        const fileNameDisplay = document.getElementById('file-name');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropArea.classList.add('border-blue-500', 'bg-blue-50');
        }

        function unhighlight() {
            dropArea.classList.remove('border-blue-500', 'bg-blue-50');
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

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
                        preview.src = e.target.result;
                        dropContent.classList.add('hidden');
                        previewContainer.classList.remove('hidden');
                        fileNameDisplay.textContent = "Nueva imagen seleccionada: " + file.name;
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("Solo se permiten imágenes.");
                }
            }
        }

        function removePreview(event) {
            if (event) event.stopPropagation();
            fileInput.value = '';
            preview.src = '#';
            dropContent.classList.remove('hidden');
            previewContainer.classList.add('hidden');
            fileNameDisplay.textContent = '';
        }

        // ATM-style currency input logic
        const montoInput = document.getElementById('monto');

        montoInput.addEventListener('input', () => {
            let value = montoInput.value.replace(/\D/g, ''); // Remove all non-digits

            if (value === '') {
                value = '0';
            }

            // Parse to float and divide by 100 to shift decimals
            let amount = (parseInt(value) / 100).toFixed(2);

            montoInput.value = amount;
        });

        montoInput.addEventListener('blur', () => {
            if (montoInput.value === '' || isNaN(montoInput.value)) {
                montoInput.value = '0.00';
            }
        });

        document.querySelectorAll('.guardar').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>