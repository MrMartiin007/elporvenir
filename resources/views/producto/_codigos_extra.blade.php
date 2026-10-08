{{--
    Códigos adicionales del producto (se usa al registrar y al editar).
    Un mismo producto puede llegar con otro código de barras (por ejemplo, en otro viaje).
    Los códigos adicionales se guardan en la tabla producto_codigos; el principal es el campo "Código del Producto".
    Ningún código puede repetirse: el formulario avisa en el momento y el servidor lo vuelve a comprobar al guardar.
--}}
@php
    $enEdicion = isset($producto) && $producto->exists;
    $extrasActuales = array_values((array) old('codigos_extra', $enEdicion ? $producto->codigos->pluck('codigo')->all() : []));
@endphp

<div class="mt-4 p-3 rounded-lg border" id="bloqueCodigos" data-url="{{ route('productos.codigo') }}"
    data-ignorar="{{ $enEdicion ? $producto->id : '' }}" style="background:#fff8fb; border-color:#f3c4d8 !important;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <strong style="color:#880e4f;"><i class="fas fa-barcode me-1"></i> Códigos adicionales</strong>
            <span class="text-muted small ms-1">(opcional)</span>
        </div>
        <button type="button" id="btnAgregarCodigo" class="btn btn-sm" style="color:#d63384; border:1px solid #d63384; background:#fff;">
            <i class="fas fa-plus me-1"></i> Agregar otro código
        </button>
    </div>
    <div class="mt-2"></div>

    <div id="listaCodigos">
        @foreach($extrasActuales as $i => $codigo)
            <div class="codigo-fila" data-fila>
                <div class="input-group mb-1">
                    <span class="input-group-text bg-white"><i class="fas fa-barcode" style="color:#d63384;"></i></span>
                    <input type="text" name="codigos_extra[]" class="form-control" maxlength="100" autocomplete="off"
                        value="{{ $codigo }}" placeholder="Otro código del mismo producto">
                    <button type="button" class="btn btn-outline-danger" data-quitar title="Quitar este código"><i class="fas fa-times"></i></button>
                </div>
                <div class="small codigo-msg" data-msg style="color:#dc3545;">{{ $errors->first('codigos_extra.' . $i) }}</div>
            </div>
        @endforeach
    </div>
</div>

<script>
    (function () {
        var bloque = document.getElementById('bloqueCodigos');
        if (!bloque) return;
        var lista = document.getElementById('listaCodigos');
        var principal = document.getElementById('codigo_producto');
        var url = bloque.getAttribute('data-url');
        var ignorar = bloque.getAttribute('data-ignorar');
        var temporizadores = new WeakMap();

        function norm(c) {
            c = (c || '').trim();
            return /^\d+$/.test(c) ? (c.replace(/^0+/, '') || c) : c.toLowerCase();
        }

        function plantilla(valor) {
            var div = document.createElement('div');
            div.className = 'codigo-fila';
            div.setAttribute('data-fila', '');
            div.innerHTML =
                '<div class="input-group mb-1">' +
                '<span class="input-group-text bg-white"><i class="fas fa-barcode" style="color:#d63384;"></i></span>' +
                '<input type="text" name="codigos_extra[]" class="form-control" maxlength="100" autocomplete="off" placeholder="Otro código del mismo producto">' +
                '<button type="button" class="btn btn-outline-danger" data-quitar title="Quitar este código"><i class="fas fa-times"></i></button>' +
                '</div><div class="small codigo-msg" data-msg style="color:#dc3545;"></div>';
            div.querySelector('input').value = valor || '';
            return div;
        }

        function mensajeDe(input) {
            if (input === principal) {
                var m = input.parentElement.querySelector('[data-msg-principal]');
                if (!m) {
                    m = document.createElement('div');
                    m.className = 'small mt-1';
                    m.setAttribute('data-msg-principal', '');
                    input.insertAdjacentElement('afterend', m);
                }
                return m;
            }
            return input.closest('[data-fila]').querySelector('[data-msg]');
        }

        function mostrar(input, texto, ok) {
            var m = mensajeDe(input);
            m.textContent = texto || '';
            m.style.color = ok ? '#198754' : '#dc3545';
            // Mientras haya un problema, el navegador no deja enviar el formulario
            input.setCustomValidity(ok || !texto ? '' : texto);
        }

        function todosLosCampos() { return [principal].concat([].slice.call(lista.querySelectorAll('input[name="codigos_extra[]"]'))); }

        function repetidoEnFormulario(input) {
            var v = norm(input.value);
            if (!v) return false;
            return todosLosCampos().some(function (otro) { return otro !== input && norm(otro.value) === v; });
        }

        function verificar(input) {
            clearTimeout(temporizadores.get(input));
            var valor = input.value.trim();
            if (!valor) { mostrar(input, '', true); return; }
            if (repetidoEnFormulario(input)) { mostrar(input, 'Este código está repetido en este mismo formulario.', false); return; }
            mostrar(input, 'Comprobando…', true); input.setCustomValidity('');
            temporizadores.set(input, setTimeout(function () {
                var q = url + '?codigo=' + encodeURIComponent(valor) + (ignorar ? '&ignorar=' + encodeURIComponent(ignorar) : '');
                fetch(q, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
                    .then(function (d) {
                        if (input.value.trim() !== valor) return;
                        if (d.disponible) mostrar(input, 'Código disponible.', true);
                        else mostrar(input, d.mensaje, false);
                    })
                    .catch(function () { mostrar(input, '', true); });
            }, 350));
        }

        function revisarTodos() { todosLosCampos().forEach(function (c) { if (c.value.trim()) verificar(c); }); }

        function agregar(valor) {
            var fila = plantilla(valor);
            lista.appendChild(fila);
            fila.querySelector('input').focus();
            return fila;
        }

        document.getElementById('btnAgregarCodigo').addEventListener('click', function () { agregar(''); });

        lista.addEventListener('input', function (e) {
            if (e.target.matches('input[name="codigos_extra[]"]')) { verificar(e.target); }
        });

        // Con lector de código de barras: el Enter que manda el lector abre otro campo en lugar de guardar el formulario
        lista.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.matches('input[name="codigos_extra[]"]')) {
                e.preventDefault();
                if (e.target.value.trim()) agregar('');
            }
        });

        lista.addEventListener('click', function (e) {
            var fila = e.target.closest('[data-fila]');
            if (!fila) return;
            var input = fila.querySelector('input');
            if (e.target.closest('[data-quitar]')) {
                input.setCustomValidity('');
                fila.remove();
                revisarTodos();
            }
        });

        if (principal) {
            principal.addEventListener('input', function () { verificar(principal); revisarTodos(); });
            principal.addEventListener('blur', function () { verificar(principal); });
        }

        // Si la página vuelve con errores del servidor, los campos que lo necesiten se marcan para impedir reenviar igual
        lista.querySelectorAll('[data-msg]').forEach(function (m) {
            if (m.textContent.trim()) { var i = m.closest('[data-fila]').querySelector('input'); i.classList.add('is-invalid'); }
        });
    })();
</script>
