<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Empresa;
use App\Models\Cheque;
use App\Models\TarjetaCredito;
use App\Models\Deposito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class FacturaController
 * @package App\Http\Controllers
 */
class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buscar = request('buscar');

        $facturas = Factura::with('empresa')
            ->when($buscar, function ($query, $buscar) {
                $query->where('numero_factura', 'like', "%{$buscar}%")
                    ->orWhereHas('empresa', function ($q) use ($buscar) {
                        $q->where('nombre_empresa', 'like', "%{$buscar}%");
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends(['buscar' => $buscar]);

        return view('factura.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $factura = new Factura();
        $empresas = Empresa::all();
        return view('factura.create', compact('factura', 'empresas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_factura' => 'required|unique:facturas,numero_factura|string|max:255',
            'monto' => 'required|numeric',
            'empresas_id' => 'required',
            'foto_fac' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('foto_fac');
        $data['estado'] = 1;

        if ($request->hasFile('foto_fac')) {
            $data['foto_fac'] = $request->file('foto_fac')->store('facturas', 'public');
        }

        Factura::create($data);

        return redirect()->route('facturas.index')
            ->with('success', 'Factura creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $factura = Factura::with(['empresa', 'cheque', 'tarjetaCredito', 'deposito'])->findOrFail($id);
        return view('factura.show', compact('factura'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $factura = Factura::find($id);
        $empresas = Empresa::all();
        return view('factura.edit', compact('factura', 'empresas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        $request->validate([
            'numero_factura' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'empresas_id' => 'required',
            'estado' => 'required',
            'foto_fac' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('foto_fac');

        if ($request->hasFile('foto_fac')) {
            // Delete old image if exists
            if ($factura->foto_fac && Storage::disk('public')->exists($factura->foto_fac)) {
                Storage::disk('public')->delete($factura->foto_fac);
            }
            $data['foto_fac'] = $request->file('foto_fac')->store('facturas', 'public');
        }

        $factura->update($data);

        return redirect()->route('facturas.index')
            ->with('success', 'Factura actualizada exitosamente.');
    }

    /**
     * Anula (soft delete logic for status) the specified resource from storage.
     * Renaming destroy or just updating logic. Keeping destroy name for route consistency but changing logic.
     */
    public function destroy($id)
    {
        $factura = Factura::find($id);
        $factura->estado = 0; // Anulada
        $factura->save();

        return redirect()->route('facturas.index')
            ->with('success', 'Factura anulada exitosamente.');
    }

    // --- Liquidation Methods ---

    public function liquidar($id)
    {
        $factura = Factura::findOrFail($id);
        return view('factura.liquidar', compact('factura'));
    }

    public function pagarEfectivo($id)
    {
        $factura = Factura::findOrFail($id);
        $factura->estado = 2; // Pagada en Efectivo
        $factura->save();

        return redirect()->route('facturas.index')
            ->with('success', 'Factura liquidada en efectivo exitosamente.');
    }

    public function pagarCheque(Request $request)
    {
        $request->validate([
            'facturas_id' => 'required|exists:facturas,id',
            'no_cheque' => 'required|string|unique',
            'fecha_cobro' => 'required|date',
            'foto_ch' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['estado'] = 1; // Estado del cheque

        if ($request->hasFile('foto_ch')) {
            $data['foto_ch'] = $request->file('foto_ch')->store('cheques', 'public');
        }

        Cheque::create($data);

        $factura = Factura::findOrFail($request->facturas_id);
        $factura->estado = 3; // Pagada con Cheque
        $factura->save();

        return redirect()->route('facturas.index')
            ->with('success', 'Factura liquidada con cheque exitosamente.');
    }

    public function pagarTarjeta(Request $request)
    {
        $request->validate([
            'facturas_id' => 'required|exists:facturas,id',
            'no_autorizacion' => 'unique|required|string',
            'fecha_pago' => 'required|date',
            'foto_tc' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['estado'] = 1; // Estado de la transaccion tarjeta

        if ($request->hasFile('foto_tc')) {
            $data['foto_tc'] = $request->file('foto_tc')->store('tarjetas', 'public');
        }

        TarjetaCredito::create($data);

        $factura = Factura::findOrFail($request->facturas_id);
        $factura->estado = 4; // Pagada con Tarjeta
        $factura->save();

        return redirect()->route('facturas.index')
            ->with('success', 'Factura liquidada con tarjeta de crédito exitosamente.');
    }

    public function pagarDeposito(Request $request)
    {
        $request->validate([
            'facturas_id' => 'required|exists:facturas,id',
            'no_deposito' => 'unique|required|string',
            'fecha_deposito' => 'required|date',
            'foto_deposito' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_deposito')) {
            $data['foto_deposito'] = $request->file('foto_deposito')->store('depositos', 'public');
        }

        Deposito::create($data);

        $factura = Factura::findOrFail($request->facturas_id);
        $factura->estado = 5; // Pagada con Depósito
        $factura->save();

        return redirect()->route('facturas.index')
            ->with('success', 'Factura liquidada con depósito exitosamente.');
    }
}
