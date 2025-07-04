<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Models\Marca;
use App\Models\Producto;
use App\Http\Requests\ProductoRequest;

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
    $marcas = Marca::pluck('nombre_marca', 'id');
    $productos = Producto::with(['marca', 'ultimaEntrada'])
        ->orderBy('created_at', 'desc')
        ->paginate(10); // o el número que desees por página

    return view('producto.index', compact('productos', 'marcas'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $producto = new Producto();
        $marcas = Marca::all();
        return view('producto.create', compact('producto', 'marcas'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(ProductoRequest $request)
    {

        $data = $request->validated();

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

        return redirect()->route('productos.index')
            ->with('success', 'Producto y entrada inicial creados exitosamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::find($id);

        return view('producto.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $producto = Producto::find($id);

        return view('producto.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto updated successfully');
    }

    public function destroy($id)
    {
        Producto::find($id)->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto deleted successfully');
    }
}
