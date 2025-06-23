@extends('layouts.app')

@section('template_title')
    {{ $entrada->name ?? __('Show') . " " . __('Entrada') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Entrada</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('entradas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                        <div class="form-group mb-2 mb20">
                            <strong>Fecha Ingreso:</strong>
                            {{ $entrada->fecha_ingreso }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Cantidad:</strong>
                            {{ $entrada->cantidad }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Productos Id:</strong>
                            {{ $entrada->productos_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
