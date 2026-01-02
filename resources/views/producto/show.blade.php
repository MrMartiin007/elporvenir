<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Detalle del Producto') }}
        </h2>
    </x-slot>


    <div class="container-fluid py-1">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">

                <!-- 🧾 Encabezado principal -->
                <div class="p-3 mb-2 rounded-4 shadow-sm text-white"
                    style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="mb-2"><i class="fas fa-box-open me-2"></i>Detalle del Producto</h3>
                        <a href="{{ route('productos.index') }}" class="btn btn-light text-dark fw-semibold shadow-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <!-- 📦 Tarjeta de información -->
                <div class="bg-white rounded-4 shadow-sm p-4">

                    <!-- Foto centrada -->
                    <div class="text-center mb-4">
                        <label class="form-label fw-bold text-muted">Foto del Producto</label><br>
                        <img src="{{ asset('storage/' . $producto->foto_producto) }}" alt="Foto"
                            class="img-fluid d-block mx-auto"
                            style="max-height: 200px; border-radius: 12px; border: 4px solid #fbc2eb; background: #fff;">

                    </div>


                    <div class="row gy-4 mb-4">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Código:</label>
                            <div class="bg-light p-3 rounded fw-semibold">{{ $producto->codigo_producto }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted">Descripción:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->detalle_producto ?? 'No especificada' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted">Marca:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->marca->nombre_marca ?? 'Sin marca' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted">Stock:</label>
                            <div class="bg-light p-3 rounded fw-semibold">{{ $producto->stock }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Precio Costo:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->ultimaEntrada?->precio_costo ? 'Q ' . number_format($producto->ultimaEntrada->precio_costo, 2) : '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Precio Venta:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->ultimaEntrada?->precio_venta ? 'Q ' . number_format($producto->ultimaEntrada->precio_venta, 2) : '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted">Precio por Docena:</label>
                            <div class="bg-light p-3 rounded fw-semibold">
                                {{ $producto->ultimaEntrada?->precio_docena ? 'Q ' . number_format($producto->ultimaEntrada->precio_docena, 2) : '-' }}
                            </div>
                        </div>

                    </div>

                    <hr class="my-4" style="border-top: 2px dashed #fbc2eb;">

                    <!-- 📝 Entradas recientes -->
                    <div class="mb-3 text-center">
                        <h5 class="fw-bold text-muted">
                            <i class="fas fa-clipboard-list me-2"></i>Últimas Entradas Registradas
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($producto->entradas && $producto->entradas->count() > 0)
                            <div class="table-responsive">
                                <table id="entradas-table" class="table table-hover nowrap"
                                    style="width:100%; border-radius: 12px; overflow: hidden;">
                                    <thead
                                        style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); color: white;">
                                        <tr class="table-warning">

                                            <th>#</th>
                                            <th>Fecha de Ingreso</th>
                                            <th>Cantidad</th>
                                            <th>Precio Costo</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Docena</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($producto->entradas as $entrada)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($entrada->fecha_ingreso)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $entrada->cantidad }}</td>
                                                <td>Q {{ number_format($entrada->precio_costo, 2) }}</td>
                                                <td>Q {{ number_format($entrada->precio_venta, 2) }}</td>
                                                <td>Q {{ number_format($entrada->precio_docena, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center py-3 mb-0 text-muted fst-italic">No hay entradas
                                registradas para este producto.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

      <script>
        $(document).ready(function() {
            $('#entradas-table').DataTable({
                paging: true,
                pageLength: 5,
                lengthChange: false,
                searching: false,
                info: false,
                ordering: true,
                responsive: true, // 👈 Esto ayuda en móviles
                language: {
                    paginate: {
                        next: 'Siguiente',
                        previous: 'Anterior'
                    },
                    emptyTable: "No hay entradas registradas",
                    zeroRecords: "No se encontraron coincidencias",
                }
            });
        });
    </script>
</x-app-layout>