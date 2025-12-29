<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Marca') }}
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

                    <form class="guardar" action="{{ route('marcas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nombre_marca" :value="__('Nombre de la Marca')" />
                                <x-text-input id="nombre_marca" class="block mt-1 w-full" type="text"
                                    name="nombre_marca" :value="old('nombre_marca')" required
                                    autocomplete="nombre_marca" placeholder="Ingrese el nombre de la marca" />
                                <x-input-error :messages="$errors->get('nombre_marca')" class="mt-2" />
                            </div>

                            <div class="mb-2">
                                <x-input-label for="foto_marca" :value="__('Logo de la Marca')" />
                                <div id="drop-area" class="mt-1 border-2 border-dashed border-gray-300 rounded-lg p-1 
                                    text-center cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <div id="drop-content">
                                        <svg class="mx-auto h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Arrastra y suelta el logo</p>
                                        <p class="mt-1 text-xs text-gray-500">o haz clic para seleccionar</p>
                                        <input type="file" id="foto_marca" name="foto_marca" class="hidden" accept="image/*" />
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
                                <x-input-error :messages="$errors->get('foto_marca')" class="mt-2" />
                            </div>

                            <div class="flex items-center mt-6 space-x-4">
                                <a href="{{ url()->previous() }}">
                                    <button type="button"
                                        class="bg-red-500 hover:bg-red-800 text-white font-bold py-2 px-4 border border-red-700 rounded">
                                        Regresar
                                    </button>
                                </a>

                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-800 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                                    Guardar Marca
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Elementos del drag & drop
        const dropArea = document.getElementById('drop-area');
        const dropContent = document.getElementById('drop-content');
        const previewContainer = document.getElementById('preview-container');
        const fileInput = document.getElementById('foto_marca');
        const preview = document.getElementById('foto_preview');
        const fileNameDisplay = document.getElementById('file-name');
        let currentFile = null;

        // Prevenir comportamientos por defecto
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Resaltar el área de drop
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

        // Manejar el drop
        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                handleFiles(files);
            }
        }

        // Manejar click para seleccionar archivo
        dropArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
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
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                alert("Solo se permiten imágenes.");
                removePreview(); // Resetear vista si el archivo no es válido
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
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                let timerInterval;
                Swal.fire({
                    title: 'Cargando...',
                    text: 'Por favor espera un momento',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        form.submit();
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        console.log("I was closed by the timer");
                    }
                });
            });
        });
    </script>
</x-app-layout>