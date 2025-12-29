@props(['id', 'name'])

<div class="drop-area border-2 border-dashed border-gray-300 rounded-lg p-1 text-center cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out relative group" 
     id="drop-area-{{ $id }}" 
     data-input-id="{{ $id }}">
    
    <div id="drop-content-{{ $id }}">
        <div class="mb-1 text-gray-400 group-hover:text-indigo-500 transition">
            <svg class="mx-auto h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                </path>
            </svg>
        </div>
        <p class="text-xs font-medium text-gray-700">Arrastra o selecciona imagen</p>
    </div>

    <div id="preview-container-{{ $id }}" class="hidden relative">
        <img id="preview-{{ $id }}" src="#" alt="Vista previa" class="mx-auto max-w-full h-auto rounded-lg shadow-sm" style="max-height: 150px;">
        <p id="filename-{{ $id }}" class="mt-1 text-xs text-gray-600 font-medium truncate px-2"></p>
        
        <button type="button" id="remove-{{ $id }}" 
            class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-0.5 shadow-md transition transform hover:scale-110" 
            title="Eliminar imagen">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <input type="file" id="{{ $id }}" name="{{ $name }}" class="hidden" accept="image/*" />
</div>