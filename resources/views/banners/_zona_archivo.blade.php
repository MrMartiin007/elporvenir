{{--
    Zona de carga de un archivo con vista previa (mismo estilo de arrastrar y soltar de marcas y productos).
    Parámetros:
      $nombre     name e id del campo (imagen, imagen_movil, video)
      $etiqueta   título del campo
      $ayuda      texto de ayuda debajo
      $actual     URL del archivo ya guardado (al editar), o null
      $tipo       'imagen' | 'video'
      $maxMb      tamaño máximo en MB (se avisa antes de enviar)
      $ratio      proporción recomendada ancho/alto (solo imágenes; avisa si la imagen elegida es muy distinta)
      $textoRatio cómo se llama esa proporción, ej. "3 a 1 (1920 × 640 px)"
      $quitable   true si se permite quitar el archivo guardado (se envía quitar_{nombre})
      $conTexto   true para dibujar sobre la vista previa el título/subtítulo/botón como se verán en la portada
--}}
@php
    $tipo = $tipo ?? 'imagen';
    $esVideo = $tipo === 'video';
    $maxMb = $maxMb ?? 4;
@endphp

<div class="mb-2" data-zona data-tipo="{{ $tipo }}" data-max-mb="{{ $maxMb }}" data-actual="{{ $actual ?? '' }}"
    data-ratio="{{ $ratio ?? '' }}" data-ratio-texto="{{ $textoRatio ?? '' }}" data-con-texto="{{ !empty($conTexto) ? '1' : '' }}">
    <x-input-label :for="$nombre" :value="$etiqueta" />

    <div class="zona-area mt-1 border-2 border-dashed border-gray-300 rounded-lg p-3 text-center cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out">
        {{-- Sin archivo todavía --}}
        <div data-vacio class="{{ !empty($actual) ? 'hidden' : '' }}">
            <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="mt-1 text-sm text-gray-600">Arrastra y suelta {{ $esVideo ? 'el video' : 'la imagen' }}</p>
            <p class="mt-1 text-xs text-gray-500">o haz clic para seleccionar</p>
        </div>

        {{-- Vista previa --}}
        <div data-vista class="{{ !empty($actual) ? '' : 'hidden' }}">
            <div class="relative inline-block max-w-full" data-marco>
                @if($esVideo)
                    <video data-media src="{{ $actual ?? '' }}" muted controls playsinline preload="metadata"
                        class="mx-auto max-w-full h-auto rounded-lg" style="max-height: 200px;"></video>
                @else
                    <img data-media src="{{ $actual ?? '#' }}" alt="Vista previa" class="mx-auto max-w-full h-auto rounded-lg" style="max-height: 200px;">
                    @if(!empty($conTexto))
                        {{-- Así se verá el texto sobre la imagen en la portada --}}
                        <div data-superposicion class="hidden absolute text-left" style="left: 0; bottom: 0; max-width: 85%; padding: .6rem .8rem .9rem; pointer-events: none;">
                            <div data-sp-titulo style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.15rem; line-height: 1.15; color: #212529;"></div>
                            <div data-sp-sub style="font-size: .72rem; color: #212529; margin-top: 2px;"></div>
                            <span data-sp-boton style="display: none; background: #333; color: #fff; border-radius: 50px; padding: 2px 12px; font-size: .68rem; font-weight: 600; margin-top: 5px;"></span>
                        </div>
                    @endif
                @endif
            </div>
            <p data-nombre class="mt-2 text-sm text-gray-700">{{ $esVideo ? 'Video actual' : 'Imagen actual' }}</p>
            <p data-detalle class="text-xs text-gray-500"></p>
            <div class="mt-1 flex items-center justify-center gap-4 text-xs">
                <button type="button" data-cambiar class="text-blue-600 hover:text-blue-800">Cambiar {{ $esVideo ? 'video' : 'imagen' }}</button>
                <button type="button" data-descartar class="hidden text-red-500 hover:text-red-700">Descartar selección</button>
                @if(!empty($quitable) && !empty($actual))
                    <label class="flex items-center gap-1 text-red-500 cursor-pointer">
                        <input type="checkbox" name="quitar_{{ $nombre }}" value="1" data-quitar> Quitar {{ $esVideo ? 'el video' : 'esta imagen' }}
                    </label>
                @endif
            </div>
        </div>

        <input type="file" id="{{ $nombre }}" name="{{ $nombre }}" class="hidden"
            accept="{{ $esVideo ? 'video/mp4,video/webm' : 'image/jpeg,image/png,image/webp' }}"
            @if(!empty($requerido) && empty($actual)) required @endif />
    </div>

    @if(!empty($ayuda))
        <p class="mt-1 text-xs text-gray-500">{{ $ayuda }}</p>
    @endif
    <p data-aviso class="mt-1 text-xs hidden" style="color: #b45309;"></p>
    <p data-error class="mt-1 text-sm text-red-600 hidden"></p>
    <x-input-error :messages="$errors->get($nombre)" class="mt-2" />
</div>
