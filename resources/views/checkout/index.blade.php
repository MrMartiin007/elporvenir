@extends('layouts.shop')

@section('content')
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4"><i class="fas fa-clipboard-check me-2"></i>Finalizar Pedido</h1>

                {{-- Resumen del pedido --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="fas fa-shopping-bag me-2"></i>Resumen de tu Pedido</h5>

                        @foreach($carrito as $id => $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <strong>{{ $item['nombre'] }}</strong>
                                    <small class="text-muted d-block">{{ $item['marca'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="d-block">{{ $item['cantidad'] }} x Q.
                                        {{ number_format($item['precio'], 2) }}</span>
                                    <strong style="color: var(--bs-primary-dark);">Q.
                                        {{ number_format($item['precio'] * $item['cantidad'], 2) }}</strong>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Subtotal:</span>
                                <span>Q. {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span>Q. {{ number_format($envio, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong class="h5 mb-0">Total:</strong>
                                <strong class="h5 mb-0" style="color: var(--bs-primary-dark);">Q.
                                    {{ number_format($total, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulario de datos del cliente --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-4"><i class="fas fa-user me-2"></i>Tus Datos</h5>

                        <form id="checkout-form" action="{{ route('cart.checkout.procesar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="g-recaptcha-response" id="recaptcha-token">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre"
                                    name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Juan Pérez" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono / WhatsApp <span
                                        class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono"
                                    name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 5555-5555" required>
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Te contactaremos por WhatsApp para coordinar la entrega</small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico (Opcional)</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="tuEmail@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="departamento_id" class="form-label">Departamento <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('departamento_id') is-invalid @enderror"
                                        id="departamento_id" name="departamento_id" required>
                                        <option value="">Selecciona un departamento</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                                                {{ $departamento->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departamento_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="municipio_id" class="form-label">Municipio <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('municipio_id') is-invalid @enderror"
                                        id="municipio_id" name="municipio_id" required disabled>
                                        <option value="">Primero selecciona un departamento</option>
                                    </select>
                                    @error('municipio_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección de Entrega <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion"
                                    name="direccion" rows="3" placeholder="Calle, número, zona, referencias..."
                                    required>{{ old('direccion') }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Incluye referencias para ubicar tu casa fácilmente</small>
                            </div>

                            <div class="mb-4">
                                <label for="notas" class="form-label">Notas Adicionales (Opcional)</label>
                                <textarea class="form-control @error('notas') is-invalid @enderror" id="notas" name="notas"
                                    rows="2"
                                    placeholder="Instrucciones especiales, horario preferido, etc.">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Importante:</strong> Al confirmar tu pedido, nos pondremos en contacto contigo por
                                WhatsApp para coordinar el pago y la entrega.
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-theme py-3">
                                    <i class="fas fa-paper-plane me-2"></i>Confirmar Pedido
                                </button>
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Carrito
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Mobile-First Form Styles */
        .form-control,
        .form-select {
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(216, 164, 164, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 767px) {

            .form-control,
            .form-select,
            .btn {
                min-height: 48px;
                /* Touch-friendly */
            }
        }
    </style>

    <script>
        // Cargar municipios cuando se selecciona un departamento
        document.getElementById('departamento_id').addEventListener('change', function () {
            const departamentoId = this.value;
            const municipioSelect = document.getElementById('municipio_id');

            if (!departamentoId) {
                municipioSelect.disabled = true;
                municipioSelect.innerHTML = '<option value="">Primero selecciona un departamento</option>';
                return;
            }

            // Mostrar loading
            municipioSelect.disabled = true;
            municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';

            // Cargar municipios vía AJAX
            fetch(`/cart/checkout/municipios/${departamentoId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Selecciona un municipio</option>';
                    data.forEach(municipio => {
                        options += `<option value="${municipio.id}">${municipio.nombre}</option>`;
                    });
                    municipioSelect.innerHTML = options;
                    municipioSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    municipioSelect.innerHTML = '<option value="">Error al cargar municipios</option>';
                });
        });
    </script>

    {{-- reCAPTCHA v3 --}}
    @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            document.getElementById('checkout-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');

                // Evitar doble submit
                if (form.dataset.submitting === 'true') return;
                form.dataset.submitting = 'true';

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

                let submitted = false;

                function doSubmit() {
                    if (!submitted) {
                        submitted = true;
                        form.submit();
                    }
                }

                // Timeout de seguridad: si reCAPTCHA no responde en 5s, enviar igual
                const timeout = setTimeout(function () {
                    console.warn('reCAPTCHA timeout, enviando formulario sin token');
                    doSubmit();
                }, 5000);

                try {
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.ready(function () {
                            grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'checkout' })
                                .then(function (token) {
                                    clearTimeout(timeout);
                                    document.getElementById('recaptcha-token').value = token;
                                    doSubmit();
                                })
                                .catch(function (err) {
                                    console.warn('reCAPTCHA error:', err);
                                    clearTimeout(timeout);
                                    doSubmit();
                                });
                        });
                    } else {
                        clearTimeout(timeout);
                        doSubmit();
                    }
                } catch (err) {
                    console.warn('reCAPTCHA exception:', err);
                    clearTimeout(timeout);
                    doSubmit();
                }
            });
        </script>
        <style>
            .grecaptcha-badge {
                visibility: hidden;
            }
        </style>
    @endif
@endsection