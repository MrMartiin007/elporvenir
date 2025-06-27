<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Http\Requests\DetalleVentaRequest;

/**
 * Class DetalleVentaController
 * @package App\Http\Controllers
 */
class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalleVentas = DetalleVenta::paginate();

        return view('detalle-venta.index', compact('detalleVentas'))
            ->with('i', (request()->input('page', 1) - 1) * $detalleVentas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $detalleVenta = new DetalleVenta();
        return view('detalle-venta.create', compact('detalleVenta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DetalleVentaRequest $request)
    {
        DetalleVenta::create($request->validated());

        return redirect()->route('detalle-ventas.index')
            ->with('success', 'DetalleVenta created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detalleVenta = DetalleVenta::find($id);

        return view('detalle-venta.show', compact('detalleVenta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $detalleVenta = DetalleVenta::find($id);

        return view('detalle-venta.edit', compact('detalleVenta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DetalleVentaRequest $request, DetalleVenta $detalleVenta)
    {
        $detalleVenta->update($request->validated());

        return redirect()->route('detalle-ventas.index')
            ->with('success', 'DetalleVenta updated successfully');
    }

    public function destroy($id)
    {
        DetalleVenta::find($id)->delete();

        return redirect()->route('detalle-ventas.index')
            ->with('success', 'DetalleVenta deleted successfully');
    }
}
