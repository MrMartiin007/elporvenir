{{-- Formulario compartido de banner/video. Recibe $banner y $action; $method es PUT al editar. --}}
@php
    $esVideo = $banner->tipo === \App\Models\Banner::TIPO_VIDEO;
    $url = fn ($ruta) => $ruta ? asset('storage/' . $ruta) : null;
    $botonClase = 'font-bold py-2 px-4 border rounded text-white';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="guardar">
    @csrf
    @if(($method ?? 'POST') !== 'POST') @method($method) @endif
    <input type="hidden" name="tipo" value="{{ $banner->tipo }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="titulo" :value="__('Título *')" />
            <x-text-input id="titulo" class="block mt-1 w-full" type="text" name="titulo"
                :value="old('titulo', $banner->titulo)" maxlength="120" required autofocus />
            <p class="mt-1 text-xs text-gray-500">
                Google lo usa como descripción de la imagen.
                @unless($esVideo) Si tu imagen ya trae el texto, deja el subtítulo y el botón vacíos y no se dibuja nada encima. @endunless
            </p>
            <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="orden" :value="__('Orden')" />
            <x-text-input id="orden" class="block mt-1 w-full" type="number" name="orden" min="0" max="9999"
                :value="old('orden', $banner->orden)" />
            <p class="mt-1 text-xs text-gray-500">El número más bajo sale primero.</p>
            <x-input-error :messages="$errors->get('orden')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="subtitulo" :value="__('Subtítulo (opcional)')" />
            <x-text-input id="subtitulo" class="block mt-1 w-full" type="text" name="subtitulo"
                :value="old('subtitulo', $banner->subtitulo)" maxlength="200" />
            <x-input-error :messages="$errors->get('subtitulo')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="texto_boton" :value="__('Texto del botón (opcional)')" />
            <x-text-input id="texto_boton" class="block mt-1 w-full" type="text" name="texto_boton"
                :value="old('texto_boton', $banner->texto_boton)" maxlength="30" placeholder="Ver productos" />
            <x-input-error :messages="$errors->get('texto_boton')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="enlace" :value="__('Enlace (opcional)')" />
            <x-text-input id="enlace" class="block mt-1 w-full" type="text" name="enlace"
                :value="old('enlace', $banner->enlace)" maxlength="255" placeholder="/tienda  ·  /marca/57-kativa  ·  https://…" />
            <p class="mt-1 text-xs text-gray-500">A dónde lleva al tocar el banner. Para una marca copia su dirección desde la tienda, por ejemplo <code>/marca/57-kativa</code>.</p>
            <x-input-error :messages="$errors->get('enlace')" class="mt-2" />
        </div>

        @if($esVideo)
            @include('banners._zona_archivo', [
                'nombre' => 'video', 'tipo' => 'video', 'etiqueta' => 'Archivo de video *',
                'actual' => $url($banner->video), 'requerido' => true, 'maxMb' => 20,
                'ayuda' => 'MP4 o WebM, máximo 20 MB, de 10 a 20 segundos y sin sonido (se reproduce en silencio). Si es más pesado, súbelo comprimido.',
            ])
            @include('banners._zona_archivo', [
                'nombre' => 'imagen', 'etiqueta' => 'Póster (imagen que se ve antes de reproducir)',
                'actual' => $url($banner->imagen), 'quitable' => true, 'maxMb' => 4,
                'ratio' => 16 / 9, 'textoRatio' => '16 a 9 (1280 × 720 px)',
                'ayuda' => 'Recomendado 1280 × 720 px, JPG o WebP, máximo 4 MB.',
            ])
        @else
            @include('banners._zona_archivo', [
                'nombre' => 'imagen', 'etiqueta' => 'Imagen principal *',
                'actual' => $url($banner->imagen), 'requerido' => true, 'maxMb' => 4, 'conTexto' => true,
                'ratio' => 3, 'textoRatio' => '3 a 1 (1920 × 640 px)',
                'ayuda' => '1920 × 640 px (3 a 1), JPG o WebP, máximo 4 MB. Deja los textos importantes en el centro: en pantallas pequeñas los bordes se recortan.',
            ])
            @include('banners._zona_archivo', [
                'nombre' => 'imagen_movil', 'etiqueta' => 'Imagen para celular (opcional)',
                'actual' => $url($banner->imagen_movil), 'quitable' => true, 'maxMb' => 4,
                'ratio' => 0.8, 'textoRatio' => 'vertical 4 a 5 (800 × 1000 px)',
                'ayuda' => '800 × 1000 px (vertical). Para que se use, súbela en todos los banners; si falta en alguno, el celular muestra las imágenes principales (recortadas a lo ancho).',
            ])
        @endif

        <div>
            <x-input-label for="inicia_en" :value="__('Mostrar desde (opcional)')" />
            <x-text-input id="inicia_en" class="block mt-1 w-full" type="datetime-local" name="inicia_en"
                :value="old('inicia_en', $banner->inicia_en?->format('Y-m-d\TH:i'))" />
            <x-input-error :messages="$errors->get('inicia_en')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="termina_en" :value="__('Mostrar hasta (opcional)')" />
            <x-text-input id="termina_en" class="block mt-1 w-full" type="datetime-local" name="termina_en"
                :value="old('termina_en', $banner->termina_en?->format('Y-m-d\TH:i'))" />
            <p class="mt-1 text-xs text-gray-500">Útil para ofertas de temporada: se apaga solo.</p>
            <x-input-error :messages="$errors->get('termina_en')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <div class="p-3 bg-light border rounded d-flex align-items-center" style="background:#f8f9fa;">
                <label class="flex items-center gap-2 cursor-pointer m-0">
                    <input type="checkbox" id="activo" name="activo" value="1" class="rounded border-gray-300"
                        @checked(old('activo', $banner->activo))>
                    <span class="text-sm font-semibold text-gray-700">Activo (se muestra en la portada)</span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end mt-6 gap-2">
        <a href="{{ route('banners.index') }}"
            class="bg-red-500 hover:bg-red-800 text-white font-bold py-2 px-4 border border-red-700 rounded">Regresar</a>
        <button type="submit"
            class="bg-blue-500 hover:bg-blue-800 text-white font-bold py-2 px-4 border border-blue-700 rounded">
            {{ ($method ?? 'POST') === 'POST' ? 'Guardar' : 'Actualizar' }}
        </button>
    </div>
</form>

<script>
    (function () {
        const form = document.querySelector('form.guardar');
        const tit = document.getElementById('titulo');
        const sub = document.getElementById('subtitulo');
        const bot = document.getElementById('texto_boton');

        const mb = (b) => b < 1048576 ? Math.max(1, Math.round(b / 1024)) + ' KB' : (b / 1048576).toFixed(1).replace('.0', '') + ' MB';

        document.querySelectorAll('[data-zona]').forEach(function (zona) {
            const area = zona.querySelector('.zona-area');
            const input = zona.querySelector('input[type=file]');
            const vacio = zona.querySelector('[data-vacio]');
            const vista = zona.querySelector('[data-vista]');
            const media = zona.querySelector('[data-media]');
            const nombre = zona.querySelector('[data-nombre]');
            const detalle = zona.querySelector('[data-detalle]');
            const descartar = zona.querySelector('[data-descartar]');
            const quitar = zona.querySelector('[data-quitar]');
            const aviso = zona.querySelector('[data-aviso]');
            const error = zona.querySelector('[data-error]');
            const sp = zona.querySelector('[data-superposicion]');
            const esVideo = zona.dataset.tipo === 'video';
            const actual = zona.dataset.actual;
            const maxMb = parseFloat(zona.dataset.maxMb);
            const ratio = parseFloat(zona.dataset.ratio) || null;
            const textoActual = nombre.textContent;
            let objUrl = null;

            const requeridoOriginal = input.required;

            function liberar() { if (objUrl) { URL.revokeObjectURL(objUrl); objUrl = null; } }

            function restaurar() {
                liberar();
                input.value = '';
                input.required = requeridoOriginal;
                aviso.classList.add('hidden');
                error.classList.add('hidden');
                descartar.classList.add('hidden');
                detalle.textContent = '';
                nombre.textContent = textoActual;
                if (actual) { media.src = actual; vacio.classList.add('hidden'); vista.classList.remove('hidden'); }
                else { media.removeAttribute('src'); vista.classList.add('hidden'); vacio.classList.remove('hidden'); }
                pintarTexto();
            }

            function mostrar(file) {
                error.classList.add('hidden');
                aviso.classList.add('hidden');
                const valido = esVideo ? /^video\/(mp4|webm)$/.test(file.type) : /^image\/(jpeg|png|webp)$/.test(file.type);
                if (!valido) {
                    error.textContent = esVideo ? 'El archivo debe ser un video MP4 o WebM.' : 'El archivo debe ser una imagen JPG, PNG o WebP.';
                    error.classList.remove('hidden');
                    restaurar();
                    error.classList.remove('hidden');
                    return;
                }
                if (file.size > maxMb * 1048576) {
                    error.textContent = 'Pesa ' + mb(file.size) + ' y el máximo es ' + maxMb + ' MB. Comprímelo e inténtalo de nuevo.';
                    restaurar();
                    error.classList.remove('hidden');
                    return;
                }
                liberar();
                objUrl = URL.createObjectURL(file);
                media.src = objUrl;
                nombre.textContent = file.name;
                detalle.textContent = mb(file.size);
                vacio.classList.add('hidden');
                vista.classList.remove('hidden');
                descartar.classList.toggle('hidden', false);
                if (quitar) quitar.checked = false;
                input.required = false;

                if (!esVideo) {
                    media.onload = function () {
                        detalle.textContent = media.naturalWidth + ' × ' + media.naturalHeight + ' px · ' + mb(file.size);
                        if (ratio) {
                            const r = media.naturalWidth / media.naturalHeight;
                            if (Math.abs(r - ratio) / ratio > 0.15) {
                                aviso.textContent = 'Ojo: la proporción de esta imagen es distinta a la recomendada (' + zona.dataset.ratioTexto + '); se recortará al mostrarse.';
                                aviso.classList.remove('hidden');
                            }
                        }
                    };
                } else {
                    media.onloadedmetadata = function () {
                        detalle.textContent = Math.round(media.duration) + ' s · ' + mb(file.size);
                        if (media.duration > 30) {
                            aviso.textContent = 'Ojo: dura más de 30 segundos; lo recomendado es de 10 a 20.';
                            aviso.classList.remove('hidden');
                        }
                    };
                }
                pintarTexto();
            }

            function pintarTexto() {
                if (!sp) return;
                const t = tit.value.trim(), s = sub.value.trim(), b = bot.value.trim();
                // Igual que la portada: solo se dibuja texto si hay subtítulo o botón.
                const ver = (s || b) && !vista.classList.contains('hidden');
                sp.classList.toggle('hidden', !ver);
                sp.querySelector('[data-sp-titulo]').textContent = t;
                sp.querySelector('[data-sp-sub]').textContent = s;
                const be = sp.querySelector('[data-sp-boton]');
                be.textContent = b; be.style.display = b ? 'inline-block' : 'none';
            }
            [tit, sub, bot].forEach(function (el) { el.addEventListener('input', pintarTexto); });
            pintarTexto();

            area.addEventListener('click', function (e) {
                if (e.target.closest('video, [data-descartar], [data-quitar], label')) return;
                input.click();
            });
            zona.querySelector('[data-cambiar]').addEventListener('click', function (e) { e.stopPropagation(); input.click(); });
            descartar.addEventListener('click', function (e) { e.stopPropagation(); restaurar(); });

            input.addEventListener('click', function (e) { e.stopPropagation(); });
            input.addEventListener('change', function () { if (input.files[0]) mostrar(input.files[0]); else restaurar(); });

            ['dragenter', 'dragover'].forEach(function (ev) {
                area.addEventListener(ev, function (e) { e.preventDefault(); area.classList.add('bg-gray-50', 'border-blue-400'); });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                area.addEventListener(ev, function (e) { e.preventDefault(); area.classList.remove('bg-gray-50', 'border-blue-400'); });
            });
            area.addEventListener('drop', function (e) {
                const f = e.dataTransfer.files[0];
                if (!f) return;
                const dt = new DataTransfer();
                dt.items.add(f);
                input.files = dt.files;
                mostrar(f);
            });

            if (quitar) {
                quitar.addEventListener('change', function () {
                    media.style.opacity = quitar.checked ? '.35' : '';
                    if (quitar.checked) input.required = false; else input.required = requeridoOriginal && !input.files.length;
                });
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!form.checkValidity()) { form.reportValidity(); return; }
            Swal.fire({
                title: 'Cargando...',
                text: 'Por favor espera un momento',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); form.submit(); }
            });
        });
    })();
</script>
