@extends('layouts.app')

@section('template_title')
    {{ $detalleVenta->name ?? __('Show') . " " . __('Detalle Venta') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Detalle Venta</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('detalle-ventas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                        <div class="form-group mb-2 mb20">
                            <strong>Cantidad:</strong>
                            {{ $detalleVenta->cantidad }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Precio Unitario:</strong>
                            {{ $detalleVenta->precio_unitario }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Productos Id:</strong>
                            {{ $detalleVenta->productos_id }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Ventas Id:</strong>
                            {{ $detalleVenta->ventas_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
