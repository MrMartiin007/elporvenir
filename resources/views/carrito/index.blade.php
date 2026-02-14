@extends('layouts.shop')

@section('content')
<div class="container my-4">

    @if(session('carrito') && count(session('carrito')) > 0)
        @php 
            $total = 0;
            $cantidadTotal = 0;
        @endphp
        
        {{-- Mobile-First: Cards para cada producto --}}
        <div class="row">
            <div class="col-lg-8">
                @foreach(session('carrito') as $id => $item)
                    @php
                        $subtotal = $item['precio'] * $item['cantidad'];
                        $total += $subtotal;
                        $cantidadTotal += $item['cantidad'];
                    @endphp
                    
                    {{-- Card de producto - Optimizado para móvil --}}
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                {{-- Imagen del producto --}}
                                <div class="col-4 col-md-2">
                                    @if($item['imagen'])
                                        <img src="{{ asset('storage/' . $item['imagen']) }}" 
                                             alt="{{ $item['nombre'] }}" 
                                             class="img-fluid rounded">
                                    @else
                                        <div class="bg-light p-3 rounded text-center">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Información del producto --}}
                                <div class="col-8 col-md-10">
                                    <div class="row">
                                        <div class="col-12 col-md-5">
                                            <h6 class="mb-1">{{ $item['nombre'] }}</h6>
                                            <small class="text-muted">{{ $item['marca'] }}</small>
                                            <p class="mb-1 mt-2">
                                                <strong style="color: var(--bs-primary-dark);">
                                                    Q. {{ number_format($item['precio'], 2) }}
                                                </strong>
                                            </p>
                                        </div>
                                        
                                        {{-- Cantidad (Mobile: debajo, Desktop: al lado) --}}
                                        <div class="col-6 col-md-3 mt-2 mt-md-0">
                                            <form action="{{ route('cart.actualizar') }}" method="POST" class="d-flex align-items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <label class="me-2 small d-none d-md-inline">Cant:</label>
                                                <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" 
                                                       min="1" max="99"
                                                       class="form-control form-control-sm" 
                                                       style="width: 70px;"
                                                       onchange="this.form.submit()">
                                            </form>
                                        </div>
                                        
                                        {{-- Subtotal y Eliminar --}}
                                        <div class="col-6 col-md-4 mt-2 mt-md-0 text-end">
                                            <p class="mb-2 fw-bold" style="color: var(--bs-primary-dark);">
                                                Q. {{ number_format($subtotal, 2) }}
                                            </p>
                                            <form action="{{ route('cart.eliminar', $id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('¿Eliminar este producto?')">
                                                    <i class="fas fa-trash"></i>
                                                    <span class="d-none d-md-inline ms-1">Eliminar</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{-- Botón Vaciar Carrito (Mobile-First) --}}
                <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline"></span>Seguir Comprando
                    </a>
                    <form action="{{ route('cart.vaciar') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" 
                                onclick="return confirm('¿Vaciar todo el carrito?')">
                            <i class="fas fa-trash-alt me-1"></i>
                            <span class="d-none d-sm-inline"> </span>Vaciar Carrito
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Resumen del Pedido - Sticky en escritorio --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-receipt me-2"></i>Resumen
                        </h5>
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos ({{ $cantidadTotal }}):</span>
                            <span>Q. {{ number_format($total, 2) }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Envío:</span>
                            <span>Q. {{ isset($tarifaActual) ? number_format($tarifaActual->costo, 2) : '35.00' }}</span>
                        </div>
                        
                        <hr>
                        
                        @php
                            $costoEnvio = isset($tarifaActual) ? $tarifaActual->costo : 35.00;
                            $totalConEnvio = $total + $costoEnvio;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0">Total:</span>
                            <span class="h5 mb-0 fw-bold" style="color: var(--bs-primary-dark);">
                                Q. {{ number_format($totalConEnvio, 2) }}
                            </span>
                        </div>
                        
                        <a href="{{ route('cart.checkout.index') }}" class="btn btn-theme w-100 py-3 mb-2">
                            <i class="fas fa-check-circle me-2"></i>Finalizar Pedido
                        </a>
                        
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 ">
                            <i class="fas fa-shopping-bag me-1"></i>Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    @else
        {{-- Carrito Vacío --}}
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-4x mb-4 text-muted"></i>
                        <h3 class="mb-3">Tu carrito está vacío</h3>
                        <p class="text-muted mb-4">
                            ¡Descubre nuestros increíbles productos de belleza y comienza a comprar!
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-theme px-5 py-3">
                            <i class="fas fa-shopping-bag me-2"></i>Ver Productos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    /* Mobile-First Styles */
    .card {
        border-radius: 12px;
    }
    
    /* Touch-friendly buttons en móvil */
    @media (max-width: 767px) {
        .btn {
            min-height: 44px; /* Apple's recommended touch target size */
        }
        
        .form-control-sm {
            min-height: 44px;
            font-size: 1rem;
        }
    }
    
    /* Sticky sidebar solo en desktop */
    @media (min-width: 992px) {
        .sticky-top {
            position: sticky;
        }
    }
</style>
@endsection
