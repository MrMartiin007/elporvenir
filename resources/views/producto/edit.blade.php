<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="py-10">
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

                    <form class="guardar" action="{{ route('productos.update', $producto->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="max-w-4xl w-full mx-auto sm:px-4 lg:px-6">
                            <!-- Foto del producto -->
                            <!-- dentro del formulario -->
                            <div class="mb-2">
                                <x-input-label for="foto_producto" :value="__('Foto del Producto')" />
                                <div id="drop-area"
                                    class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <div id="drop-content">
                                        <svg class="mx-auto h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Arrastra y suelta la foto o haz clic para
                                            seleccionar</p>
                                        <input type="file" id="foto_producto" name="foto_producto" class="hidden"
                                            accept="image/*" />
                                    </div>

                                    <div id="preview-container"
                                        class="mt-2 {{ $producto->foto_producto ? '' : 'hidden' }}">
                                        <img id="foto_preview"
                                            src="{{ $producto->foto_producto ? asset('storage/' . $producto->foto_producto) : '#' }}"
                                            alt="Vista previa" class="mx-auto max-w-full h-auto rounded-lg"
                                            style="max-height: 150px;">
                                     
                                        <button type="button" onclick="removePreview(event)"
                                            class="mt-2 text-xs text-red-500 hover:text-red-700">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('foto_producto')" class="mt-2" />
                            </div>


                            <!-- Inputs -->
                            <div class="space-y-6">
                                <div class="flex flex-wrap justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="detalle_producto" :value="__('Detalle del Producto')" />
                                        <x-text-input id="detalle_producto" class="block mt-1 w-full" type="text"
                                            name="detalle_producto" :value="old('detalle_producto', $producto->detalle_producto)" required />
                                        <x-input-error :messages="$errors->get('detalle_producto')" class="mt-2" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="marcas_id" :value="__('Marca del Producto')" />
                                        <select name="marcas_id" id="marcas_id"
                                            class="select2 block mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm">
                                            <option value="">Selecciona una marca</option>
                                            @foreach ($marcas as $marca)
                                                <option value="{{ $marca->id }}" {{ $producto->marcas_id == $marca->id ? 'selected' : '' }}>
                                                    {{ $marca->nombre_marca }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('marcas_id')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex-1 min-w-0">
                                        <x-input-label for="codigo_producto" :value="__('Código del Producto')" />
                                        <x-text-input id="codigo_producto" class="block mt-1 w-full" type="text"
                                            name="codigo_producto" :value="old('codigo_producto', $producto->codigo_producto)" required />
                                        <x-input-error :messages="$errors->get('codigo_producto')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center mt-6 space-x-4">
                                <a href="{{ route('productos.index') }}">
                                    <button type="button"
                                        class="bg-red-500 hover:bg-red-800 text-white font-bold py-2 px-4 border border-red-700 rounded">
                                        Cancelar
                                    </button>
                                </a>

                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-800 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                                    Actualizar Producto
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    const dropArea = document.getElementById('drop-area');
    const dropContent = document.getElementById('drop-content');
    const previewContainer = document.getElementById('preview-container');
    const fileInput = document.getElementById('foto_producto');
    const preview = document.getElementById('foto_preview');
    const fileNameDisplay = document.getElementById('file-name');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    dropArea.addEventListener('click', () => fileInput.click());

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const files = e.dataTransfer.files;
        if (files.length) {
            handleFiles(files);
        }
    }

    fileInput.addEventListener('change', function () {
        if (this.files.length) {
            handleFiles(this.files);
        }
    });

    function handleFiles(files) {
        const file = files[0];
        const isImage = file.type.startsWith('image/');
        dropContent.classList.add('hidden');
        previewContainer.classList.remove('hidden');
        fileNameDisplay.textContent = file.name;

        if (isImage) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            alert('Solo se permiten imágenes.');
            removePreview();
        }
    }

    function removePreview(event) {
        if (event) event.stopPropagation();
        fileInput.value = '';
        preview.src = '#';
        preview.style.display = 'none';
        dropContent.classList.remove('hidden');
        previewContainer.classList.add('hidden');
        fileNameDisplay.textContent = '';
    }
</script>
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

</x-app-layout>