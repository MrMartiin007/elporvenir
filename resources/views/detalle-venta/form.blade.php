<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
            <input type="text" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', $detalleVenta?->cantidad) }}" id="cantidad" placeholder="Cantidad">
            {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="precio_unitario" class="form-label">{{ __('Precio Unitario') }}</label>
            <input type="text" name="precio_unitario" class="form-control @error('precio_unitario') is-invalid @enderror" value="{{ old('precio_unitario', $detalleVenta?->precio_unitario) }}" id="precio_unitario" placeholder="Precio Unitario">
            {!! $errors->first('precio_unitario', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="productos_id" class="form-label">{{ __('Productos Id') }}</label>
            <input type="text" name="productos_id" class="form-control @error('productos_id') is-invalid @enderror" value="{{ old('productos_id', $detalleVenta?->productos_id) }}" id="productos_id" placeholder="Productos Id">
            {!! $errors->first('productos_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="ventas_id" class="form-label">{{ __('Ventas Id') }}</label>
            <input type="text" name="ventas_id" class="form-control @error('ventas_id') is-invalid @enderror" value="{{ old('ventas_id', $detalleVenta?->ventas_id) }}" id="ventas_id" placeholder="Ventas Id">
            {!! $errors->first('ventas_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>