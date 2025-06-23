<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Producto</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('productos.update', $producto->id) }}" role="form"
                            enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            <div class="row padding-1 p-1">
                                <div class="col-md-12">

                                    <div class="form-group mb-2 mb20">
                                        <label for="codigo_producto"
                                            class="form-label">{{ __('Codigo Producto') }}</label>
                                        <input type="text" name="codigo_producto"
                                            class="form-control @error('codigo_producto') is-invalid @enderror"
                                            value="{{ old('codigo_producto', $producto?->codigo_producto) }}"
                                            id="codigo_producto" placeholder="Codigo Producto">
                                        {!! $errors->first('codigo_producto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label for="detalle_producto"
                                            class="form-label">{{ __('Detalle Producto') }}</label>
                                        <input type="text" name="detalle_producto"
                                            class="form-control @error('detalle_producto') is-invalid @enderror"
                                            value="{{ old('detalle_producto', $producto?->detalle_producto) }}"
                                            id="detalle_producto" placeholder="Detalle Producto">
                                        {!! $errors->first('detalle_producto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label for="foto_producto" class="form-label">{{ __('Foto Producto') }}</label>
                                        <input type="text" name="foto_producto"
                                            class="form-control @error('foto_producto') is-invalid @enderror"
                                            value="{{ old('foto_producto', $producto?->foto_producto) }}"
                                            id="foto_producto" placeholder="Foto Producto">
                                        {!! $errors->first('foto_producto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label for="precio_costo" class="form-label">{{ __('Precio Costo') }}</label>
                                        <input type="text" name="precio_costo"
                                            class="form-control @error('precio_costo') is-invalid @enderror"
                                            value="{{ old('precio_costo', $producto?->precio_costo) }}"
                                            id="precio_costo" placeholder="Precio Costo">
                                        {!! $errors->first('precio_costo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label for="precio_venta" class="form-label">{{ __('Precio Venta') }}</label>
                                        <input type="text" name="precio_venta"
                                            class="form-control @error('precio_venta') is-invalid @enderror"
                                            value="{{ old('precio_venta', $producto?->precio_venta) }}"
                                            id="precio_venta" placeholder="Precio Venta">
                                        {!! $errors->first('precio_venta', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>
                                    <div class="form-group mb-2 mb20">
                                        <label for="precio_docena" class="form-label">{{ __('Precio Docena') }}</label>
                                        <input type="text" name="precio_docena"
                                            class="form-control @error('precio_docena') is-invalid @enderror"
                                            value="{{ old('precio_docena', $producto?->precio_docena) }}"
                                            id="precio_docena" placeholder="Precio Docena">
                                        {!! $errors->first('precio_docena', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                    </div>

                                </div>
                                <div class="col-md-12 mt20 mt-2">
                                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>