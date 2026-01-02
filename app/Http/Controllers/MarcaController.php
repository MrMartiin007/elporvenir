<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Http\Requests\MarcaRequest;

/**
 * Class MarcaController
 * @package App\Http\Controllers
 */
class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buscar = request('buscar');

        $marcas = Marca::where('nombre_marca', 'like', "%{$buscar}%")

            ->orderBy('nombre_marca', 'asc')
            ->paginate(10); // o el número que desees por página

        return view('marca.index', compact('marcas'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $marca = new Marca();
        return view('marca.create', compact('marca'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MarcaRequest $request)
    {
        $data = $request->validated();

        // Procesar la imagen si existe
        if ($request->hasFile('foto_marca')) {
            $imagePath = $request->file('foto_marca')->store('marcas', 'public');
            $data['foto_marca'] = $imagePath;
        }

        Marca::create($data);

        return redirect()->route('marcas.index')
            ->with('success', 'Marca created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $marca = Marca::find($id);

        return view('marca.show', compact('marca'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $marca = Marca::find($id);

        return view('marca.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MarcaRequest $request, Marca $marca)
    {
        $data = $request->validated();

        // Procesar la imagen si se subió una nueva
        if ($request->hasFile('foto_marca')) {
            // Eliminar la imagen anterior si existe
            if ($marca->foto_marca && \Storage::disk('public')->exists($marca->foto_marca)) {
                \Storage::disk('public')->delete($marca->foto_marca);
            }

            // Guardar la nueva imagen
            $imagePath = $request->file('foto_marca')->store('marcas', 'public');
            $data['foto_marca'] = $imagePath;
        }

        $marca->update($data);

        return redirect()->route('marcas.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy($id)
    {
        Marca::find($id)->delete();

        return redirect()->route('marcas.index')
            ->with('success', 'Marca deleted successfully');
    }
}
