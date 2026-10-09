{{--
    Buscador inteligente de productos del panel. Sugiere mientras escribes (código, nombre o marca, palabras en cualquier orden).
    Si falla el JavaScript funciona como un formulario normal (?buscar=). El lector de código de barras sigue igual:
    escribe el código y manda Enter, y el formulario se envía.
    Parámetros:
      $destino     'listado' (lista de productos) | 'consultar' (pantalla Consultar)
      $grande      true para la versión grande de Consultar
      $placeholder texto de ayuda
      $valor       texto que se muestra al cargar
      $enfocar     true para enfocar el campo al abrir la página
--}}
@php
    $accion = ($destino ?? 'listado') === 'consultar' ? route('productos.consultar') : route('productos.index');
    $uid = 'ab' . \Illuminate\Support\Str::random(5);
    $grande = $grande ?? false;
@endphp
<form method="GET" action="{{ $accion }}" role="search" class="adm-bus {{ $grande ? 'adm-bus--grande' : '' }}" data-adm-buscador
    data-url="{{ route('productos.sugerencias') }}" data-ir="{{ $accion }}" autocomplete="off">
    <div class="input-group {{ $grande ? 'input-group-lg' : '' }}">
        <span class="input-group-text bg-white" style="border-color: #d63384;">
            <i class="fas {{ $grande ? 'fa-barcode' : 'fa-search' }}" style="color: #d63384;" aria-hidden="true"></i>
        </span>
        <input type="search" name="buscar" value="{{ $valor ?? request('buscar') }}" maxlength="60"
            class="form-control" style="border-color: #d63384; box-shadow: none;" enterkeyhint="search"
            placeholder="{{ $placeholder ?? 'Código, nombre o marca…' }}" aria-label="Buscar productos"
            role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="{{ $uid }}"
            @if($enfocar ?? false) autofocus @endif>
        <button type="submit" class="btn text-white px-3" style="background-color: #d63384; border-color: #d63384;">
            <i class="fas fa-search {{ $grande ? 'me-2' : '' }}" aria-hidden="true"></i>@if($grande) Buscar @endif
        </button>
    </div>
    <div id="{{ $uid }}" class="adm-bus-lista" role="listbox" hidden></div>
</form>

@once
<style>
    .adm-bus { position: relative; }
    .adm-bus-lista {
        position: absolute; left: 0; right: 0; top: calc(100% + 6px); z-index: 1060;
        background: #fff; border: 1px solid #f3c4d8; border-radius: 14px;
        box-shadow: 0 14px 34px rgba(136, 14, 79, .16); overflow: hidden auto; max-height: min(70vh, 520px);
        min-width: 340px; text-align: left;
    }
    .adm-bus-lista[hidden] { display: none; }
    .adm-bus-fila { display: flex; align-items: center; border-bottom: 1px solid #fbe9f1; }
    .adm-bus-item { flex: 1; min-width: 0; display: flex; align-items: center; gap: .75rem; padding: .55rem .8rem; color: #33282a; text-decoration: none; }
    .adm-bus-fila:hover, .adm-bus-fila.adm-act { background: #fff3f8; }
    .adm-bus-foto { flex: 0 0 44px; width: 44px; height: 44px; border-radius: 8px; overflow: hidden; background: #fce4ec; display: flex; align-items: center; justify-content: center; color: #e8a5c3; }
    .adm-bus-foto img { width: 100%; height: 100%; object-fit: cover; }
    .adm-bus-txt { flex: 1; min-width: 0; display: flex; flex-direction: column; line-height: 1.25; }
    .adm-bus-nom { font-weight: 600; font-size: .92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .adm-bus-sub { font-size: .75rem; color: #8a7b73; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .adm-bus-der { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; white-space: nowrap; }
    .adm-bus-precio { font-weight: 700; color: #880e4f; font-size: .9rem; }
    .adm-bus-stock { font-size: .72rem; font-weight: 600; border-radius: 50px; padding: 1px 8px; color: #fff; }
    .adm-st2 { background: #d63384; } .adm-st1 { background: #e0a100; } .adm-st0 { background: #dc3545; }
    .adm-bus-edit { flex: 0 0 auto; margin-right: .6rem; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #198754; border: 1px solid #b7dcc8; text-decoration: none; }
    .adm-bus-edit:hover { background: #198754; color: #fff; }
    .adm-bus-todos { display: block; padding: .65rem 1rem; text-align: center; font-weight: 600; color: #d63384; text-decoration: none; }
    .adm-bus-todos:hover, .adm-bus-todos.adm-act { background: #fff3f8; }
    .adm-bus-vacio { padding: 1rem; color: #6c757d; font-size: .92rem; }
    .adm-bus--grande .adm-bus-lista { min-width: 0; }
    @media (max-width: 575px) { .adm-bus-lista { min-width: 0; } }
</style>
<script>
    (function () {
        function el(tag, clase, texto) { var e = document.createElement(tag); if (clase) e.className = clase; if (texto != null) e.textContent = texto; return e; }

        function iniciar(form) {
            if (form.dataset.listo) return;
            form.dataset.listo = '1';
            var input = form.querySelector('input[name="buscar"]');
            var lista = form.querySelector('.adm-bus-lista');
            var url = form.getAttribute('data-url'), ir = form.getAttribute('data-ir');
            var temporizador = null, pedido = null, activo = -1;

            function cerrar() { lista.hidden = true; input.setAttribute('aria-expanded', 'false'); activo = -1; }
            function filas() { return lista.querySelectorAll('[data-fila]'); }
            function marcar(i) {
                var fs = filas(); if (!fs.length) return;
                activo = (i + fs.length) % fs.length;
                fs.forEach(function (f, n) { f.classList.toggle('adm-act', n === activo); });
                fs[activo].scrollIntoView({ block: 'nearest' });
            }
            function destino(codigo) { return ir + '?buscar=' + encodeURIComponent(codigo); }

            function pintar(d, q) {
                lista.textContent = ''; activo = -1;
                d.productos.forEach(function (p) {
                    var fila = el('div', 'adm-bus-fila'); fila.setAttribute('data-fila', '1');
                    var a = el('a', 'adm-bus-item'); a.href = destino(p.codigo); a.setAttribute('role', 'option');
                    var foto = el('span', 'adm-bus-foto');
                    if (p.foto) { var img = document.createElement('img'); img.src = p.foto; img.alt = ''; img.loading = 'lazy'; img.width = 44; img.height = 44; foto.appendChild(img); }
                    else { foto.appendChild(el('i', 'fas fa-image')); }
                    var txt = el('span', 'adm-bus-txt');
                    txt.appendChild(el('span', 'adm-bus-nom', p.nombre));
                    txt.appendChild(el('span', 'adm-bus-sub', (p.alterno ? p.alterno + ' (código adicional de ' + p.codigo + ')' : p.codigo) + (p.marca ? ' · ' + p.marca : '') + (p.oferta ? ' · en oferta' : '')));
                    var der = el('span', 'adm-bus-der');
                    der.appendChild(el('span', 'adm-bus-precio', p.precio ? 'Q ' + p.precio : 'Sin precio'));
                    der.appendChild(el('span', 'adm-bus-stock ' + (p.stock <= 0 ? 'adm-st0' : (p.stock <= 5 ? 'adm-st1' : 'adm-st2')), p.stock <= 0 ? 'Sin stock' : 'Stock ' + p.stock));
                    a.appendChild(foto); a.appendChild(txt); a.appendChild(der);
                    var ed = el('a', 'adm-bus-edit'); ed.href = p.editar; ed.title = 'Editar este producto'; ed.setAttribute('aria-label', 'Editar ' + p.nombre);
                    ed.appendChild(el('i', 'fa fa-edit'));
                    fila.appendChild(a); fila.appendChild(ed); lista.appendChild(fila);
                });
                if (!d.productos.length) lista.appendChild(el('div', 'adm-bus-vacio', 'No hay productos con «' + q + '». Revisa el código o prueba con otra palabra.'));
                var todos = el('a', 'adm-bus-todos', d.productos.length ? (d.total > d.productos.length ? 'Ver los ' + d.total + ' resultados' : 'Ver solo estos resultados') : 'Buscar de todos modos');
                todos.href = destino(q); todos.setAttribute('data-fila', '1');
                lista.appendChild(todos);
                lista.hidden = false; input.setAttribute('aria-expanded', 'true');
            }

            function buscar() {
                var q = input.value.trim();
                if (q.length < 2) { cerrar(); return; }
                if (pedido) pedido.abort();
                pedido = new AbortController();
                fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' }, signal: pedido.signal, credentials: 'same-origin' })
                    .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
                    .then(function (d) { if (input.value.trim() === q) pintar(d, q); })
                    .catch(function () { /* sin conexión o cancelado: el formulario normal sigue funcionando */ });
            }

            input.addEventListener('input', function () { clearTimeout(temporizador); temporizador = setTimeout(buscar, 220); });
            input.addEventListener('keydown', function (e) {
                if (lista.hidden) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); marcar(activo + 1); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); marcar(activo - 1); }
                else if (e.key === 'Escape') { cerrar(); }
                else if (e.key === 'Enter' && activo >= 0) { e.preventDefault(); filas()[activo].querySelector('a').click(); }
                // Sin fila marcada, Enter envía el formulario (así funciona el lector de código de barras)
            });
            input.addEventListener('focus', function () { if (lista.children.length && input.value.trim().length >= 2) { lista.hidden = false; input.setAttribute('aria-expanded', 'true'); } });
            document.addEventListener('click', function (e) { if (!form.contains(e.target)) cerrar(); });
        }

        function todos() { document.querySelectorAll('[data-adm-buscador]').forEach(iniciar); }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', todos); else todos();
    })();
</script>
@endonce
