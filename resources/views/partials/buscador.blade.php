{{--
    Buscador con sugerencias mientras se escribe. Si falla el JavaScript, funciona como un formulario normal hacia /tienda.
    Parámetros opcionales:
      $variante    'grande' (por defecto) | 'compacto' (barra lateral) | 'nav' (menú, abre la lista hacia la izquierda)
      $placeholder texto de ayuda del campo
    Puede haber varios en la misma página: cada uno tiene su propia lista. El CSS está en shop-buscador.css.
--}}
@php
    $uid = 'bl' . \Illuminate\Support\Str::random(5);
    $clases = ['grande' => '', 'compacto' => 'buscador--compacto', 'nav' => 'buscador--compacto buscador--derecha'][$variante ?? 'grande'] ?? '';
@endphp
<form action="{{ route('tienda') }}" method="GET" role="search" class="buscador {{ $clases }}" data-buscador
    data-url="{{ route('tienda.buscar') }}">
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    <div class="buscador-box">
        <i class="fas fa-search" aria-hidden="true"></i>
        <input type="search" name="search" autocomplete="off" maxlength="60" enterkeyhint="search"
            value="{{ request('search') }}"
            placeholder="{{ $placeholder ?? 'Busca productos o marcas: alisado, pestañol, Kativa…' }}"
            aria-label="Buscar productos o marcas" role="combobox" aria-expanded="false"
            aria-autocomplete="list" aria-controls="{{ $uid }}">
        <button type="submit">Buscar</button>
    </div>
    <div id="{{ $uid }}" class="buscador-lista" role="listbox" hidden></div>
</form>

@once
<script>
    (function () {
        function el(tag, clase, texto) {
            var e = document.createElement(tag);
            if (clase) e.className = clase;
            if (texto != null) e.textContent = texto;
            return e;
        }

        function iniciar(form) {
            if (form.dataset.listo) return;
            form.dataset.listo = '1';

            var input = form.querySelector('input[name="search"]');
            var lista = form.querySelector('.buscador-lista');
            var url = form.getAttribute('data-url');
            var temporizador = null, pedido = null, activo = -1;

            function cerrar() {
                lista.hidden = true;
                input.setAttribute('aria-expanded', 'false');
                activo = -1;
            }

            function opciones() { return lista.querySelectorAll('[role="option"]'); }

            function marcar(i) {
                var ops = opciones();
                if (!ops.length) return;
                activo = (i + ops.length) % ops.length;
                ops.forEach(function (o, n) { o.classList.toggle('sug-activa', n === activo); o.setAttribute('aria-selected', n === activo); });
                ops[activo].scrollIntoView({ block: 'nearest' });
            }

            function pintar(datos, q) {
                lista.textContent = '';
                activo = -1;
                var hay = datos.productos.length || datos.marcas.length;

                if (datos.marcas.length) {
                    lista.appendChild(el('div', 'sug-titulo', 'Marcas'));
                    var fila = el('div', 'sug-marcas');
                    datos.marcas.forEach(function (m) {
                        var a = el('a', 'sug-marca', m.nombre);
                        a.href = m.url; a.setAttribute('role', 'option');
                        fila.appendChild(a);
                    });
                    lista.appendChild(fila);
                }

                if (datos.productos.length) {
                    lista.appendChild(el('div', 'sug-titulo', 'Productos'));
                    datos.productos.forEach(function (p) {
                        var a = el('a', 'sug-item'); a.href = p.url; a.setAttribute('role', 'option');
                        var foto = el('span', 'sug-foto');
                        if (p.imagen) {
                            var img = document.createElement('img');
                            img.src = p.imagen; img.alt = ''; img.loading = 'lazy'; img.width = 44; img.height = 44;
                            foto.appendChild(img);
                        } else {
                            foto.appendChild(el('i', 'fas fa-image'));
                        }
                        var texto = el('span', 'sug-texto');
                        texto.appendChild(el('span', 'sug-nombre', p.nombre));
                        texto.appendChild(el('span', 'sug-marca-txt', p.marca));
                        a.appendChild(foto); a.appendChild(texto);
                        a.appendChild(p.agotado ? el('span', 'sug-agotado', 'Agotado') : el('span', 'sug-precio', 'Q. ' + p.precio));
                        lista.appendChild(a);
                    });
                }

                if (!hay) {
                    lista.appendChild(el('div', 'sug-vacio', 'No encontramos «' + q + '». Prueba con otra palabra o una marca.'));
                }

                var todos = el('a', 'sug-todos', hay ? 'Ver todos los resultados para «' + q + '»' : 'Ver toda la tienda');
                todos.href = hay ? datos.todos : form.getAttribute('action');
                todos.setAttribute('role', 'option');
                lista.appendChild(todos);

                lista.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            }

            function buscar() {
                var q = input.value.trim();
                if (q.length < 2) { cerrar(); return; }
                if (pedido) pedido.abort();
                pedido = new AbortController();
                fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' }, signal: pedido.signal })
                    .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
                    .then(function (d) { if (input.value.trim() === q) pintar(d, q); })
                    .catch(function () { /* sin conexión o cancelado: el formulario normal sigue funcionando */ });
            }

            input.addEventListener('input', function () {
                clearTimeout(temporizador);
                temporizador = setTimeout(buscar, 220);
            });

            input.addEventListener('keydown', function (e) {
                if (lista.hidden) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); marcar(activo + 1); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); marcar(activo - 1); }
                else if (e.key === 'Escape') { cerrar(); }
                else if (e.key === 'Enter' && activo >= 0) { e.preventDefault(); opciones()[activo].click(); }
            });

            input.addEventListener('focus', function () {
                if (lista.children.length && input.value.trim().length >= 2) { lista.hidden = false; input.setAttribute('aria-expanded', 'true'); }
            });
            document.addEventListener('click', function (e) { if (!form.contains(e.target)) cerrar(); });
        }

        function iniciarTodos() { document.querySelectorAll('[data-buscador]').forEach(iniciar); }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciarTodos);
        else iniciarTodos();
    })();
</script>
@endonce
