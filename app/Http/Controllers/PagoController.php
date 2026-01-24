<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\TarjetaCredito;
use App\Models\Deposito;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    /**
     * Display a listing of all payments.
     */
    public function index()
    {
        $buscarCheque = request('buscar_cheque');
        $buscarDeposito = request('buscar_deposito');
        $buscarTarjeta = request('buscar_tarjeta');

        // Fetch Cheques with search and pagination
        $cheques = Cheque::with(['factura.empresa'])
            ->when($buscarCheque, function ($query, $buscarCheque) {
                $query->where('no_cheque', 'like', "%{$buscarCheque}%")
                    ->orWhereHas('factura.empresa', function ($q) use ($buscarCheque) {
                        $q->where('nombre_empresa', 'like', "%{$buscarCheque}%");
                    });
            })
            ->orderBy('fecha_cobro', 'desc')
            ->paginate(10)
            ->appends(['buscar_cheque' => $buscarCheque]);

        // Fetch Tarjetas with search and pagination
        $tarjetas = TarjetaCredito::with(['factura.empresa'])
            ->when($buscarTarjeta, function ($query, $buscarTarjeta) {
                $query->where('no_autorizacion', 'like', "%{$buscarTarjeta}%")
                    ->orWhereHas('factura.empresa', function ($q) use ($buscarTarjeta) {
                        $q->where('nombre_empresa', 'like', "%{$buscarTarjeta}%");
                    });
            })
            ->orderBy('fecha_pago', 'desc')
            ->paginate(10)
            ->appends(['buscar_tarjeta' => $buscarTarjeta]);

        // Fetch Depositos with search and pagination
        $depositos = Deposito::with(['factura.empresa'])
            ->when($buscarDeposito, function ($query, $buscarDeposito) {
                $query->where('no_deposito', 'like', "%{$buscarDeposito}%")
                    ->orWhereHas('factura.empresa', function ($q) use ($buscarDeposito) {
                        $q->where('nombre_empresa', 'like', "%{$buscarDeposito}%");
                    });
            })
            ->orderBy('fecha_deposito', 'desc')
            ->paginate(10)
            ->appends(['buscar_deposito' => $buscarDeposito]);

        return view('pagos.index', compact('cheques', 'tarjetas', 'depositos'));
    }
}
