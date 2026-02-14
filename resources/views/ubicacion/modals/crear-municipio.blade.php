{{-- Modal Crear Municipio --}}
<div class="modal fade" id="modalCrearMunicipio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fce4ec;">
                <h5 class="modal-title" style="color: #880e4f;">
                    <i class="fas fa-city me-2"></i>Nuevo Municipio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ubicaciones.municipio.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <div class="alert alert-info py-2">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            <strong id="departamento_nombre_display"></strong>
                        </div>
                        <input type="hidden" id="departamento_id" name="departamento_id" required>
                    </div>

                    <div class="mb-3">
                        <label for="nombre_muni" class="form-label">Nombre del Municipio <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre_muni"
                            name="nombre" required placeholder="Ej: Puerto Barrios">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="activo_muni" name="activo"
                            checked>
                        <label class="form-check-label" for="activo_muni">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background-color: #d63384;">
                        <i class="fa fa-save me-1"></i>Crear Municipio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>