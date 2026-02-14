{{-- Modal Crear Departamento --}}
<div class="modal fade" id="modalCrearDepartamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fce4ec;">
                <h5 class="modal-title" style="color: #880e4f;">
                    <i class="fas fa-map-marked-alt me-2"></i>Nuevo Departamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ubicaciones.departamento.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre_depto" class="form-label">Nombre del Departamento <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre_depto"
                            name="nombre" required placeholder="Ej: Guatemala">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="activo_depto" name="activo"
                            checked>
                        <label class="form-check-label" for="activo_depto">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background-color: #d63384;">
                        <i class="fa fa-save me-1"></i>Crear Departamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>