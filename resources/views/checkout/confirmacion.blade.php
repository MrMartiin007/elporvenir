@extends('layouts.shop')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Mensaje de éxito --}}
                <div class="text-center mb-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="mb-3">¡Pedido Realizado con Éxito!</h1>
                    <p class="lead text-muted">
                        Gracias por tu compra, <strong>{{ $pedido->nombre_cliente }}</strong>
                    </p>
                </div>

                {{-- Información del pedido --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Detalles del Pedido</h5>
                            <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.9rem;">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Número de Pedido</small>
                                <p class="mb-0 fw-bold">{{ $pedido->numero_pedido }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Fecha</small>
                                <p class="mb-0">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <hr>

                        {{-- Productos --}}
                        <h6 class="mb-3">Productos</h6>
                        @foreach($pedido->detalles as $detalle)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <strong>{{ $detalle->producto->detalle_producto ?? 'Producto' }}</strong>
                                    <small class="text-muted d-block">Cantidad: {{ $detalle->cantidad }}</small>
                                </div>
                                <span class="fw-bold" style="color: var(--bs-primary-dark);">
                                    Q. {{ number_format($detalle->subtotal, 2) }}
                                </span>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Subtotal:</span>
                                <span>Q. {{ number_format($pedido->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span>Q. {{ number_format($pedido->envio, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong class="h5 mb-0">Total:</strong>
                                <strong class="h5 mb-0" style="color: var(--bs-primary-dark);">
                                    Q. {{ number_format($pedido->total, 2) }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Información de contacto --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Datos de Contacto</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Teléfono</small>
                                <p class="mb-0">
                                    <i class="fab fa-whatsapp text-success me-2"></i>
                                    {{ $pedido->telefono_cliente }}
                                </p>
                            </div>
                            @if($pedido->email_cliente)
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted">Email</small>
                                    <p class="mb-0">
                                        <i class="fas fa-envelope me-2"></i>
                                        {{ $pedido->email_cliente }}
                                    </p>
                                </div>
                            @endif
                            <div class="col-12 mb-3">
                                <small class="text-muted">Dirección de Entrega</small>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $pedido->direccion_cliente }}
                                </p>
                            </div>
                            @if($pedido->notas_cliente)
                                <div class="col-12">
                                    <small class="text-muted">Notas</small>
                                    <p class="mb-0">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        {{ $pedido->notas_cliente }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Próximos pasos --}}
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Próximos Pasos</h5>
                    <ol class="mb-0 ps-3">
                        <li>Nos pondremos en contacto contigo por <strong>WhatsApp</strong> al número
                            {{ $pedido->telefono_cliente }}
                        </li>
                        <li>Coordinaremos el <strong>método de pago</strong> (efectivo, transferencia, etc.)</li>
                        <li>Acordaremos el <strong>horario de entrega</strong> más conveniente para ti</li>
                        <li>¡Recibirás tus productos en la puerta de tu casa!</li>
                    </ol>
                </div>

                {{-- Botones de acción --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <a href="{{ route('home') }}" class="btn btn-theme px-5 py-3">
                        <i class="fas fa-shopping-bag me-2"></i>Seguir Comprando
                    </a>
                    <a href="https://wa.me/50238995635?text=Hola,%20realicé%20el%20pedido%20*{{ $pedido->numero_pedido }}*%20por%20un%20total%20de%20Q.{{ number_format($pedido->total, 2) }}.%20¿Cuándo%20pueden%20entregarlo?"
                        target="_blank" class="btn btn-success px-5 py-3">
                        <i class="fab fa-whatsapp me-2"></i>Contactar por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .alert ol {
            padding-left: 1.2rem;
        }

        .alert ol li {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 767px) {
            .fas.fa-check-circle {
                font-size: 3.5rem !important;
            }

            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
@endsection