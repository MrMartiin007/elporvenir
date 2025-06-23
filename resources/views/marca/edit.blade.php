@extends('layouts.app')

@section('template_title')
    {{ __('Update') }} Marca
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Marca</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('marcas.update', $marca->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            <div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre_marca" class="form-label">{{ __('Nombre Marca') }}</label>
            <input type="text" name="nombre_marca" class="form-control @error('nombre_marca') is-invalid @enderror" value="{{ old('nombre_marca', $marca?->nombre_marca) }}" id="nombre_marca" placeholder="Nombre Marca">
            {!! $errors->first('nombre_marca', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="logo_marca" class="form-label">{{ __('Logo Marca') }}</label>
            <input type="text" name="logo_marca" class="form-control @error('logo_marca') is-invalid @enderror" value="{{ old('logo_marca', $marca?->logo_marca) }}" id="logo_marca" placeholder="Logo Marca">
            {!! $errors->first('logo_marca', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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
@endsection
