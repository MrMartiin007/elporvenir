<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

/**
 * Class EmpresaController
 * @package App\Http\Controllers
 */
class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empresas = Empresa::orderBy('created_at', 'desc')->get(); // Using get() for DataTables client-side or simple list
        return view('empresa.index', compact('empresas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresa = new Empresa();
        return view('empresa.create', compact('empresa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'proveedor' => 'required|string|max:255',
        ]);

        Empresa::create($request->all());

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $empresa = Empresa::find($id);
        return view('empresa.show', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $empresa = Empresa::find($id);
        return view('empresa.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'proveedor' => 'required|string|max:255',
        ]);

        $empresa->update($request->all());

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $empresa = Empresa::find($id)->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa eliminada exitosamente.');
    }
}
