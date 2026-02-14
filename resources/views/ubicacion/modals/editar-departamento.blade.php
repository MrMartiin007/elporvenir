{{-- Modal Editar Departamento --}}
<div class="modal fade" id="modalEditarDepartamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fce4ec;">
                <h5 class="modal-title" style="color: #880e4f;">
                    <i class="fas fa-edit me-2"></i>Editar Departamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre_depto" class="form-label">Nombre del Departamento <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nombre_depto" name="nombre" required>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit_activo_depto"
                            name="activo">
                        <label class="form-check-label" for="edit_activo_depto">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>