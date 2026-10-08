{{--
    Carrito sin recarga: mini-carrito lateral, avisos (toast) y contador del navbar.
    Requiere Bootstrap JS cargado antes. Si el JavaScript falla, los formularios
    siguen funcionando de forma clásica (POST + redirect).
--}}
<input type="hidden" id="cart-csrf" value="{{ csrf_token() }}">

{{-- Mini-carrito lateral --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel"
    style="--bs-offcanvas-width: 400px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title mb-0" id="miniCartLabel">
            <i class="fas fa-shopping-cart me-2" style="color: var(--bs-primary-dark);"></i> Tu carrito
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div id="miniCartBody" class="flex-grow-1 overflow-auto px-3"></div>
        <div id="miniCartFooter" class="border-top p-3 bg-white"></div>
    </div>
</div>

{{-- Avisos --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
    <div id="cartToast" class="toast align-items-center border-0" role="status" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="cartToastBody"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

<style>
    .mini-cart-item { display: flex; gap: .75rem; padding: .9rem 0; border-bottom: 1px solid #f1e6e6; }
    .mini-cart-item img { width: 64px; height: 64px; object-fit: cover; border-radius: 10px; background: #f9ebeb; flex-shrink: 0; }
    .mini-cart-item .name { font-size: .88rem; font-weight: 600; line-height: 1.25; color: #212529; }
    .mini-cart-item .brand { font-size: .75rem; color: #8a8a8a; }
    .mini-cart-qty { display: inline-flex; align-items: center; border: 1px solid #e3d3d3; border-radius: 50px; overflow: hidden; }
    .mini-cart-qty button { border: 0; background: #fff; width: 30px; height: 30px; line-height: 1; color: #495057; }
    .mini-cart-qty button:disabled { opacity: .35; }
    .mini-cart-qty span { min-width: 28px; text-align: center; font-size: .85rem; font-weight: 600; }
    .mini-cart-remove { border: 0; background: none; color: #b0657b; font-size: .78rem; padding: 0; }
    .mini-cart-loading { opacity: .5; pointer-events: none; }
    #cartToast.toast-ok { background: #1f6f4a; color: #fff; }
    #cartToast.toast-error { background: #a8323e; color: #fff; }
    #cartToast .btn-close { filter: invert(1); }
    #cartToast a { color: #fff; text-decoration: underline; white-space: nowrap; }
</style>

<script>
    (function () {
        var URLS = {
            resumen: @json(route('cart.resumen')),
            actualizar: @json(route('cart.actualizar')),
            eliminar: @json(url('/cart/eliminar')),
            carrito: @json(route('cart.index')),
            checkout: @json(route('cart.checkout.index'))
        };
        var csrf = document.getElementById('cart-csrf').value;
        var offcanvasEl = document.getElementById('miniCart');
        var bodyEl = document.getElementById('miniCartBody');
        var footerEl = document.getElementById('miniCartFooter');
        var toastEl = document.getElementById('cartToast');
        var offcanvas = new bootstrap.Offcanvas(offcanvasEl);
        var toast = new bootstrap.Toast(toastEl, { delay: 3500 });

        function esc(s) {
            var d = document.createElement('div');
            d.textContent = s == null ? '' : s;
            return d.innerHTML;
        }

        function money(n) {
            return 'Q. ' + Number(n).toLocaleString('es-GT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function request(method, url, data) {
            var opts = {
                method: method,
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            };
            if (data) {
                opts.headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(data);
            }
            return fetch(url, opts).then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (json) {
                    json.ok = res.ok && json.ok !== false;
                    if (!res.ok && !json.message) {
                        json.message = res.status === 419
                            ? 'La sesión expiró. Recarga la página e intenta de nuevo.'
                            : 'No se pudo completar la acción. Intenta de nuevo.';
                    }
                    return json;
                });
            });
        }

        function setCount(n) {
            document.querySelectorAll('.js-cart-count').forEach(function (el) {
                el.textContent = n;
                el.classList.toggle('d-none', !n);
            });
        }

        function showToast(ok, message, withLink) {
            toastEl.classList.toggle('toast-ok', ok);
            toastEl.classList.toggle('toast-error', !ok);
            document.getElementById('cartToastBody').innerHTML =
                '<i class="fas ' + (ok ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i>' +
                '<span>' + esc(message) + '</span>' +
                (ok && withLink ? ' <a href="#" data-open-cart>Ver carrito</a>' : '');
            toast.show();
        }

        function render(cart) {
            if (!cart.items || !cart.items.length) {
                bodyEl.innerHTML =
                    '<div class="text-center text-muted py-5">' +
                    '<i class="fas fa-shopping-basket fa-3x mb-3" style="color:#e3d3d3"></i>' +
                    '<p class="mb-1 fw-semibold">Tu carrito está vacío</p>' +
                    '<p class="small">Agrega productos para empezar.</p></div>';
                footerEl.innerHTML =
                    '<button class="btn btn-theme w-100" data-bs-dismiss="offcanvas">Seguir comprando</button>';
                return;
            }

            bodyEl.innerHTML = cart.items.map(function (it) {
                return '<div class="mini-cart-item" data-id="' + esc(it.id) + '">' +
                    '<img src="' + esc(it.imagen) + '" alt="' + esc(it.nombre) + '" loading="lazy" width="64" height="64">' +
                    '<div class="flex-grow-1">' +
                    '<div class="name">' + esc(it.nombre) + '</div>' +
                    '<div class="brand">' + esc(it.marca) + '</div>' +
                    '<div class="d-flex align-items-center justify-content-between mt-2">' +
                    '<div class="mini-cart-qty">' +
                    '<button type="button" data-qty="-1" aria-label="Quitar uno"' + (it.cantidad <= 1 ? ' disabled' : '') + '>&minus;</button>' +
                    '<span>' + it.cantidad + '</span>' +
                    '<button type="button" data-qty="1" aria-label="Agregar uno">+</button>' +
                    '</div>' +
                    '<strong style="font-size:.9rem">' + money(it.subtotal) + '</strong>' +
                    '</div>' +
                    '<button type="button" class="mini-cart-remove mt-1" data-remove><i class="fas fa-trash-alt me-1"></i>Quitar</button>' +
                    '</div></div>';
            }).join('');

            footerEl.innerHTML =
                '<div class="d-flex justify-content-between mb-1"><span class="text-muted">Subtotal</span>' +
                '<strong>' + money(cart.subtotal) + '</strong></div>' +
                '<p class="small text-muted mb-3">El costo de envío se calcula al finalizar la compra.</p>' +
                '<a href="' + URLS.checkout + '" class="btn btn-theme w-100 mb-2">Finalizar compra</a>' +
                '<a href="' + URLS.carrito + '" class="btn btn-outline-secondary w-100 btn-sm" style="border-radius:50px">Ver carrito completo</a>';
        }

        function apply(cart) {
            setCount(cart.count || 0);
            render(cart);
        }

        function openCart() {
            bodyEl.classList.add('mini-cart-loading');
            offcanvas.show();
            request('GET', URLS.resumen).then(function (json) {
                apply(json);
            }).finally(function () {
                bodyEl.classList.remove('mini-cart-loading');
            });
        }

        /* --- Agregar al carrito (tienda y detalle) --- */
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.matches || !form.action || form.action.indexOf('/cart/agregar') === -1) return;
            e.preventDefault();

            var btn = form.querySelector('button[type="submit"]');
            var original = btn ? btn.innerHTML : '';
            if (btn) {
                if (btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>';
            }

            var data = {
                producto_id: form.querySelector('[name="producto_id"]').value,
                cantidad: (form.querySelector('[name="cantidad"]') || {}).value || 1
            };

            request('POST', form.action, data).then(function (json) {
                if (typeof json.count === 'number') setCount(json.count);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = original;
                }
                if (json.ok) {
                    // Marca como agregado todos los botones del mismo producto en la página
                    document.querySelectorAll('form[action*="/cart/agregar"]').forEach(function (f) {
                        var campo = f.querySelector('[name="producto_id"]');
                        var b = f.querySelector('button[type="submit"]');
                        if (!campo || !b || campo.value !== data.producto_id) return;
                        var largo = /al carrito/i.test(b.textContent);
                        b.classList.remove('btn-theme');
                        b.classList.add('added');
                        b.style.backgroundColor = 'var(--bs-primary)';
                        b.style.color = '#fff';
                        b.style.border = 'none';
                        b.innerHTML = '<i class="fas fa-check ' + (largo ? 'me-2' : 'me-1') + '"></i>' +
                            (largo ? ' Agregado al carrito' : ' Agregado');
                    });
                }
                showToast(json.ok, json.message, true);
            }).catch(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = original; }
                showToast(false, 'Sin conexión. Revisa tu internet e intenta de nuevo.');
            });
        });

        /* --- Abrir mini-carrito desde el icono del navbar o el aviso --- */
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('a[title="Ver Carrito"], [data-open-cart]');
            if (!trigger) return;
            e.preventDefault();
            toast.hide();
            openCart();
        });

        /* --- Cantidad y quitar dentro del mini-carrito --- */
        bodyEl.addEventListener('click', function (e) {
            var row = e.target.closest('.mini-cart-item');
            if (!row) return;
            var id = row.getAttribute('data-id');
            var qtyBtn = e.target.closest('[data-qty]');
            var removeBtn = e.target.closest('[data-remove]');
            var promise;

            if (qtyBtn) {
                var actual = parseInt(row.querySelector('.mini-cart-qty span').textContent, 10);
                var nueva = actual + parseInt(qtyBtn.getAttribute('data-qty'), 10);
                if (nueva < 1) return;
                promise = request('PATCH', URLS.actualizar, { id: id, cantidad: nueva });
            } else if (removeBtn) {
                promise = request('DELETE', URLS.eliminar + '/' + encodeURIComponent(id));
            } else {
                return;
            }

            bodyEl.classList.add('mini-cart-loading');
            promise.then(function (json) {
                if (json.items) apply(json);
                if (!json.ok && json.message) showToast(false, json.message);
            }).catch(function () {
                showToast(false, 'Sin conexión. Revisa tu internet e intenta de nuevo.');
            }).finally(function () {
                bodyEl.classList.remove('mini-cart-loading');
            });
        });
    })();
</script>
