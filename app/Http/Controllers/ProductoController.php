<?php

namespace App\Http\Controllers;

use App\Models\CodigoNoEncontrado;
use App\Models\Entrada;
use App\Models\Marca;
use App\Models\Producto;
use App\Http\Requests\ProductoRequest;
use Illuminate\Http\Request;

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

        $productos = Producto::with(['marca', 'ultimaEntrada'])
            ->when($buscar, function ($query, $buscar) {
                $query->where('codigo_producto', 'like', "%{$buscar}%")
                    ->orWhere('detalle_producto', 'like', "%{$buscar}%")
                    ->orWhereHas('marca', function ($q) use ($buscar) {
                        $q->where('nombre_marca', 'like', "%{$buscar}%");
                    });
            })
            ->orderBy('updated_at', 'desc')
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

        $data = $request->validated();
        $data['oferta'] = $request->has('oferta') ? 1 : 0;

        // Procesar la imagen si existe
        if ($request->hasFile('foto_producto')) {
            $imagePath = $request->file('foto_producto')->store('productos', 'public');
            $data['foto_producto'] = $imagePath;
        }

        // Crear el producto
        $producto = Producto::create($data);

        // Crear la entrada inicial
        Entrada::create([
            'productos_id' => $producto->id,
            'cantidad' => $request->input('cantidad'),
            'precio_costo' => $request->input('precio_costo'),
            'precio_venta' => $request->input('precio_venta'),
            'precio_docena' => $request->input('precio_docena'),
            'fecha_ingreso' => now(),
        ]);

        // Eliminar el código de la lista de códigos no encontrados si existe
        CodigoNoEncontrado::where('codigo', $producto->codigo_producto)->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto y entrada inicial creados exitosamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::with(['marca', 'ultimaEntrada'])->findOrFail($id);

        return view('producto.show', compact('producto'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $marcas = Marca::all();
        return view('producto.edit', compact('producto', 'marcas'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'codigo_producto' => 'required|string|max:100|unique:productos,codigo_producto,' . $producto->id,
            'detalle_producto' => 'required',
            'foto_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'marcas_id' => 'required|exists:marcas,id',
            'oferta' => 'boolean',
        ]);

        // Actualizar datos básicos
        $producto->update([
            'codigo_producto' => $request->codigo_producto,
            'detalle_producto' => $request->detalle_producto,
            'marcas_id' => $request->marcas_id,
            'oferta' => $request->has('oferta') ? 1 : 0,
        ]);

        // Procesar la imagen si se subió una nueva
        if ($request->hasFile('foto_producto')) {
            // Eliminar la imagen anterior si existe
            if ($producto->foto_producto && \Storage::disk('public')->exists($producto->foto_producto)) {
                \Storage::disk('public')->delete($producto->foto_producto);
            }

            // Guardar la nueva imagen
            $rutaFoto = $request->file('foto_producto')->store('productos', 'public');
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
    public function consultarProducto()
    {
        $buscar = request('buscar');

        $productos = null;

        if ($buscar) {
            $productos = Producto::with(['marca', 'ultimaEntrada'])
                ->where('codigo_producto', 'like', "%{$buscar}%")
                ->orWhere('detalle_producto', 'like', "%{$buscar}%")
                ->orWhereHas('marca', function ($query) use ($buscar) {
                    $query->where('nombre_marca', 'LIKE', "%$buscar%");
                })
                ->get();
        }

        return view('producto.consultar', compact('productos', 'buscar'));
    }

}
