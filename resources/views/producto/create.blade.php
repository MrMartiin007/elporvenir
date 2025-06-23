<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg bg-div">
                <div class="p-6 text-gray-900">
                    @if (session('bad_status'))
                        <div class="mb-4">
                            <div class="alert alert-warning">
                                {{ session('bad_status') }}
                            </div>
                        </div>
                    @endif

                    <form class="guardar" action="{{ route('productos.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="max-w-4xl w-full mx-auto sm:px-4 lg:px-6">
                            <!-- Foto del producto - ocupa toda la línea -->
                            <div class="mb-2">
                                <x-input-label for="foto_producto" :value="__('Foto del Producto')" />
                                <div id="drop-area" class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-1 
                                    text-center cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <div id="drop-content">
                                        <svg class="mx-auto h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Arrastra y suelta la foto del producto</p>
                                        <p class="mt-1 text-xs text-gray-500">o haz clic para seleccionar</p>
                                        <input type="file" id="foto_producto" name="foto_producto" class="hidden"
                                            accept="image/*" />
                                    </div>

                                    <div id="preview-container" class="mt-2 hidden">
                                        <img id="foto_preview" src="#" alt="Vista previa"
                                            class="mx-auto max-w-full h-auto rounded-lg" style="max-height: 150px;">
                                        <p id="file-name" class="mt-1 text-sm text-gray-700 text-center"></p>

                                        <button type="button" onclick="removePreview(event)"
                                            class="mt-2 text-xs text-red-500 hover:text-red-700">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('foto_producto')" class="mt-2" />
                            </div>

                            <!-- Inputs agrupados en 2 líneas de 3 elementos cada una -->
                            <div class="space-y-6">
                                <!-- Primera fila: Detalle, Marca, Precio Costo -->
                                <div class="flex flex-wrap justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="detalle_producto" :value="__('Detalle Producto')" />
                                        <x-text-input id="detalle_producto" class="block mt-1 w-full" type="text"
                                            name="detalle_producto" :value="old('detalle_producto')" required
                                            autocomplete="detalle_producto" />
                                        <x-input-error :messages="$errors->get('detalle_producto')" class="mt-2" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="marcas_id" :value="__('Marca del Producto')" />
                                        <select name="marcas_id" id="marcas_id"
                                            class="select2 block mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm">
                                            <option value="">Selecciona una marca</option>
                                            @foreach ($marcas as $marca)
                                                <option value="{{ $marca->id }}" {{ old('marcas_id') == $marca->id ? 'selected' : '' }}>
                                                    {{ $marca->nombre_marca }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('marcas_id')" class="mt-2" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="precio_costo" :value="__('Precio Costo')" />
                                        <x-text-input id="precio_costo" class="block mt-1 w-full" type="number"
                                            name="precio_costo" :value="old('precio_costo')" required
                                            autocomplete="precio_costo" step="0.01" />
                                        <x-input-error :messages="$errors->get('precio_costo')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Segunda fila: Precio Venta, Precio Docena, Código -->
                                <div class="flex flex-wrap justify-between gap-4">

                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="precio_venta" :value="__('Precio Venta')" />
                                        <x-text-input id="precio_venta" class="block mt-1 w-full" type="number"
                                            name="precio_venta" :value="old('precio_venta')" required
                                            autocomplete="precio_venta" step="0.01" />
                                        <x-input-error :messages="$errors->get('precio_venta')" class="mt-2" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="precio_docena" :value="__('Precio por Docena')" />
                                        <x-text-input id="precio_docena" class="block mt-1 w-full" type="number"
                                            name="precio_docena" :value="old('precio_docena')" required
                                            autocomplete="precio_docena" step="0.01" />
                                        <x-input-error :messages="$errors->get('precio_docena')" class="mt-2" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="codigo_producto" :value="__('Código Producto')" />
                                        <x-text-input id="codigo_producto" class="block mt-1 w-full" type="text"
                                            name="codigo_producto" :value="old('codigo_producto')" required
                                            autocomplete="codigo_producto" />
                                        <x-input-error :messages="$errors->get('codigo_producto')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center mt-6 space-x-4">
                            <a href="{{ url()->previous() }}">
                                <button type="button"
                                    class="bg-red-500 hover:bg-red-800 text-white font-bold py-2 px-4 border border-red-700 rounded">
                                    Regresar
                                </button>
                            </a>

                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-800 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                                Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#marcas_id').select2({
                placeholder: "Selecciona una Marca",
                width: '100%',
                dropdownAutoWidth: true,
            });

            // Ajuste visual del contenedor select2 para que coincida con los inputs
            $('.select2-selection').css({
                'height': '2.5rem',
                'padding': '0.5rem 0.75rem',
                'font-size': '1rem',
                'border-radius': '0.375rem',
                'border': '1px solid #d1d5db',
                'display': 'flex',
                'align-items': 'center'
            });

            $('.select2-selection__rendered').css({
                'line-height': 'normal',
                'font-size': '1rem'
            });

            $('.select2-selection__arrow').css({
                'height': '100%'
            });
        });
    </script>

    <script>
        // Drag & drop image upload
        const dropArea = document.getElementById('drop-area');
        const dropContent = document.getElementById('drop-content');
        const previewContainer = document.getElementById('preview-container');
        const fileInput = document.getElementById('foto_producto');
        const preview = document.getElementById('foto_preview');
        const fileNameDisplay = document.getElementById('file-name');
        let currentFile = null;

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
            if (files.length) {
                handleFiles(files);
            }
        }

        dropArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length) {
                handleFiles(this.files);
            }
        });

        function handleFiles(files) {
            const file = files[0];
            currentFile = file;
            const isImage = file.type.startsWith('image/');
            dropContent.classList.add('hidden');
            previewContainer.classList.remove('hidden');
            fileNameDisplay.textContent = file.name;
            if (isImage) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                alert("Solo se permiten imágenes.");
                removePreview();
            }
        }

        function removePreview(event) {
            if (event) event.stopPropagation();
            currentFile = null;
            fileInput.value = '';
            preview.src = '#';
            preview.style.display = 'none';
            dropContent.classList.remove('hidden');
            previewContainer.classList.add('hidden');
            fileNameDisplay.textContent = '';
        }
    </script>

    <script>
        document.querySelectorAll('.guardar').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                Swal.fire({
                    title: 'Cargando...',
                    text: 'Por favor espera un momento',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                });
            });
        });
    </script>
    <script>
        const formatCurrencyInputs = ['precio_costo', 'precio_venta', 'precio_docena'];

        formatCurrencyInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.value = '0.00';

                input.addEventListener('input', () => {
                    let raw = input.value.replace(/\D/g, ''); // quitar todo lo que no sea número
                    if (raw.length === 0) raw = '0';

                    let formatted = (parseInt(raw) / 100).toFixed(2);
                    input.value = formatted;
                });

                input.addEventListener('blur', () => {
                    if (input.value === '' || isNaN(input.value)) {
                        input.value = '0.00';
                    }
                });
            }
        });
    </script>
    <script>
        const inputCosto = document.getElementById('precio_costo');
        const inputVenta = document.getElementById('precio_venta');
        const inputDocena = document.getElementById('precio_docena');

        function formatCurrency(value) {
            let raw = value.replace(/\D/g, '');
            if (raw.length === 0) raw = '0';
            return (parseInt(raw) / 100).toFixed(2);
        }

        // Formateo y cálculos automáticos desde el precio costo
        inputCosto.addEventListener('input', () => {
            const formatted = formatCurrency(inputCosto.value);
            inputCosto.value = formatted;

            let costoFloat = parseFloat(formatted);

            if (!isNaN(costoFloat)) {
                // Precio venta: 30%
                let venta = (costoFloat * 1.25).toFixed(2);
                inputVenta.value = venta;

                // Precio docena: 15%
                let docena = (costoFloat * 1.15).toFixed(2);
                inputDocena.value = docena;
            }
        });

        inputCosto.addEventListener('blur', () => {
            if (inputCosto.value === '' || isNaN(inputCosto.value)) {
                inputCosto.value = '0.00';
            }
        });

        // Formateo para los otros campos (opcional si quieres mantener el input limpio)
        [inputVenta, inputDocena].forEach(input => {
            input.value = '0.00';

            input.addEventListener('input', () => {
                input.value = formatCurrency(input.value);
            });

            input.addEventListener('blur', () => {
                if (input.value === '' || isNaN(input.value)) {
                    input.value = '0.00';
                }
            });
        });
    </script>



</x-app-layout>