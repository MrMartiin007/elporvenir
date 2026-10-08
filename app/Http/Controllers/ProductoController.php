<?php

namespace App\Http\Controllers;

use App\Models\CodigoNoEncontrado;
use App\Models\Entrada;
use App\Models\Marca;
use App\Models\Producto;
use App\Http\Requests\ProductoRequest;
use App\Services\CodigosProducto;
use App\Services\ImageVariants;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductoController
 * @package App\Http\Controllers
 */
class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buscar = request('buscar');

        $productos = Producto::with(['marca', 'ultimaEntrada', 'codigos'])
            ->when($buscar, fn ($query, $texto) => $query->buscar($texto)->porRelevancia($texto))
            ->when(!$buscar, fn ($query) => $query->orderBy('updated_at', 'desc'))
            ->paginate(10)
            ->appends(['buscar' => $buscar]); // mantiene el filtro en la paginación

        $marcas = Marca::pluck('nombre_marca', 'id');

        return view('producto.index', compact('productos', 'marcas'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $producto = new Producto();
        $marcas = Marca::all();

        // Recibir código desde query string si existe
        $codigoPrellenado = request('codigo');

        return view('producto.create', compact('producto', 'marcas', 'codigoPrellenado'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(ProductoRequest $request)
    {

        $data = Arr::except($request->validated(), ['codigos_extra']);
        $data['oferta'] = $request->has('oferta') ? 1 : 0;
        // La columna no acepta NULL: sin foto se guarda vacío (el formulario no la exige y antes daba error 500)
        $data['foto_producto'] = $data['foto_producto'] ?? '';

        // Procesar la imagen si existe
        if ($request->hasFile('foto_producto')) {
            $imagePath = $request->file('foto_producto')->store('productos', 'public');
            $data['foto_producto'] = $imagePath;
            app(ImageVariants::class)->generate($imagePath);
        }

        // Producto, códigos adicionales y entrada inicial se guardan juntos: o todo, o nada
        DB::transaction(function () use ($data, $request) {
            $producto = Producto::create($data);

            // Códigos adicionales (también quita de "códigos no encontrados" los que ya tienen dueño)
            CodigosProducto::sincronizar($producto, (array) $request->input('codigos_extra', []));

            // Crear la entrada inicial
            Entrada::create([
                'productos_id' => $producto->id,
                'cantidad' => $request->input('cantidad'),
                'precio_costo' => $request->input('precio_costo'),
                'precio_venta' => $request->input('precio_venta'),
                'precio_docena' => $request->input('precio_docena'),
                'fecha_ingreso' => now(),
            ]);
        });

        return redirect()->route('productos.index')
            ->with('success', 'Producto y entrada inicial creados exitosamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::with(['marca', 'ultimaEntrada', 'codigos'])->findOrFail($id);

        return view('producto.show', compact('producto'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $producto->load('codigos');
        $marcas = Marca::all();
        return view('producto.edit', compact('producto', 'marcas'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, Producto $producto)
    {
        // Datos básicos y códigos se guardan juntos: o todo, o nada
        DB::transaction(function () use ($request, $producto) {
            $producto->update([
                'codigo_producto' => $request->codigo_producto,
                'detalle_producto' => $request->detalle_producto,
                'marcas_id' => $request->marcas_id,
                'oferta' => $request->has('oferta') ? 1 : 0,
            ]);

            CodigosProducto::sincronizar($producto, (array) $request->input('codigos_extra', []));
        });

        // Procesar la imagen si se subió una nueva
        if ($request->hasFile('foto_producto')) {
            // Eliminar la imagen anterior si existe
            if ($producto->foto_producto && \Storage::disk('public')->exists($producto->foto_producto)) {
                \Storage::disk('public')->delete($producto->foto_producto);
            }
            app(ImageVariants::class)->delete($producto->foto_producto);

            // Guardar la nueva imagen
            $rutaFoto = $request->file('foto_producto')->store('productos', 'public');
            app(ImageVariants::class)->generate($rutaFoto);
            $producto->update(['foto_producto' => $rutaFoto]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy($id)
    {
        try {
            Producto::find($id)->delete();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto, este ya tuvo ventas registradas.');
        }
    }
    /**
     * ¿Está libre este código? Lo usa el formulario para avisar en el momento, antes de guardar.
     * /admin/producto/codigo-disponible?codigo=123&ignorar=45  (ignorar = producto que se está editando)
     */
    public function codigoDisponible(Request $request)
    {
        $codigo = trim((string) $request->query('codigo', ''));
        if ($codigo === '') {
            return response()->json(['disponible' => true, 'mensaje' => null]);
        }

        $dueno = CodigosProducto::duenoDe($codigo, $request->query('ignorar') ? (int) $request->query('ignorar') : null);

        return response()->json([
            'disponible' => $dueno === null,
            'mensaje' => $dueno ? CodigosProducto::mensajeEnUso($codigo, $dueno) : null,
        ]);
    }

    /**
     * Sugerencias del buscador del panel: /admin/producto/sugerencias?q=kativa
     * Devuelve hasta 8 productos con código, marca, stock y precio, más el total de coincidencias.
     */
    public function sugerencias(Request $request)
    {
        $q = trim(mb_substr((string) $request->query('q', ''), 0, 60));
        if (mb_strlen($q) < 2) {
            return response()->json(['productos' => [], 'total' => 0]);
        }

        $total = Producto::buscar($q)->count();

        $productos = Producto::with(['marca', 'ultimaEntrada', 'codigos'])
            ->buscar($q)
            ->porRelevancia($q)
            ->limit(8)->get()
            ->map(fn (Producto $p) => [
                'id' => $p->id,
                'codigo' => $p->codigo_producto,
                // Si lo que se buscó coincide con un código adicional, se muestra cuál
                'alterno' => $p->codigos->first(fn ($c) => stripos($c->codigo, $q) !== false)?->codigo,
                'extras' => $p->codigos->count(),
                'nombre' => $p->detalle_producto,
                'marca' => $p->marca->nombre_marca ?? '',
                'stock' => (int) $p->stock,
                'precio' => is_numeric($p->ultimaEntrada?->precio_venta) ? number_format((float) $p->ultimaEntrada->precio_venta, 2) : null,
                'oferta' => (int) $p->oferta > 0,
                'foto' => ImageVariants::existe($p->foto_producto) ? ImageVariants::url($p->foto_producto, 400) : null,
                'editar' => route('productos.edit', $p->id),
            ])->values();

        return response()->json(['productos' => $productos, 'total' => $total]);
    }

    public function consultarProducto()
    {
        $buscar = request('buscar');

        $productos = null;

        if ($buscar) {
            // Código exacto primero; máximo 20 fichas para que la pantalla no se vuelva interminable
            $productos = Producto::with(['marca', 'ultimaEntrada', 'codigos'])
                ->buscar($buscar)
                ->porRelevancia($buscar)
                ->limit(20)
                ->get();
        }

        return view('producto.consultar', compact('productos', 'buscar'));
    }

}
