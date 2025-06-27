<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use App\Http\Requests\EntradaRequest;
use App\Models\Producto;
use Illuminate\Http\Request;

/**
 * Class EntradaController
 * @package App\Http\Controllers
 */
class EntradaController extends Controller
{
    /**
     * Display a listing of the resource.
     */


public function index(Request $request)
{
    $productosFiltrados = collect();
    $ultimaEntrada = null;

    if ($request->filled('buscar')) {
        $productosFiltrados = Producto::where('codigo_producto', $request->buscar)->get();

        if ($productosFiltrados->count() === 1) {
            $producto = $productosFiltrados->first();
            $ultimaEntrada = Entrada::where('productos_id', $producto->id)
                ->latest()
                ->first();
        }
           // 🚨 Si no se encontró el producto, redirige a productos.create
        if ($productosFiltrados->isEmpty()) {
            return redirect()->route('productos.create')->with([
                'bad_status' => 'El producto con código "' . $request->buscar . '" no fue encontrado. Por favor, regístralo.',
            ]);
        }
    
    }
      


    $entradas = Entrada::with('producto')
        ->orderBy('created_at', 'asc')
        ->get();

    return view('entrada.index', compact('entradas', 'productosFiltrados', 'ultimaEntrada'));
}



    /**
     * Show the form for creating a new resource.
     */
  public function create(Request $request)
    {
        $entrada = new Entrada();
        $productos = Producto::all(); 
        $productoId = $request->get('productos_id'); // <- recibe el id
        $producto = Producto::find($productoId);

        return view('entrada.create', compact('entrada', 'productos', 'productoId','producto'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EntradaRequest $request)
    {
        Entrada::create($request->validated());
        return redirect()->route('entradas.index')
            ->with('success', 'Entrada created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $entrada = Entrada::find($id);

        return view('entrada.show', compact('entrada'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $entrada = Entrada::find($id);

        return view('entrada.edit', compact('entrada'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EntradaRequest $request, Entrada $entrada)
    {
        $entrada->update($request->validated());

        return redirect()->route('entradas.index')
            ->with('success', 'Entrada updated successfully');
    }

    public function destroy($id)
    {
        $entrada = Entrada::findOrFail($id);
        $producto = $entrada->producto;
    
        // Verificamos si el stock actual es suficiente para eliminar la entrada
        if ($producto->stock < $entrada->cantidad) {
            return redirect()->route('entradas.index')
                ->with('error', 'No se puede eliminar esta entrada, no hay suficiente stock.');
        }
    
        // Restamos la cantidad al stock del producto
        $producto->stock -= $entrada->cantidad;
        $producto->save();
    
        $entrada->delete();
    
        return redirect()->route('entradas.index')
            ->with('success', 'Entrada eliminada correctamente.');
    }
}
