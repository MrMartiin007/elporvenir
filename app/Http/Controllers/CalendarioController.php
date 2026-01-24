<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\TarjetaCredito;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        $events = [];

        // Fetch Cheques (Estado 0 = Anulado, 1 = Activo/Pagado, 2 = Confirmado)
        $cheques = Cheque::with(['factura.empresa'])->whereIn('estado', [0, 1, 2])->get();

        foreach ($cheques as $cheque) {
            if ($cheque->factura && $cheque->factura->empresa) {
                // Determine Color
                if ($cheque->estado == 2) {
                    $color = '#9ca3af'; // Gray (Confirmed)
                } elseif ($cheque->estado == 0) {
                    $color = '#ef4444'; // Red (Voided)
                } else {
                    $color = '#3b82f6'; // Blue (Pending)
                }

                $events[] = [
                    'title' => $cheque->factura->empresa->nombre_empresa . ' - Q.' . number_format($cheque->factura->monto, 2),
                    'start' => $cheque->fecha_cobro,
                    'color' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'Cheque',
                        'id' => $cheque->id,
                        'factura_id' => $cheque->factura->id,
                        'monto' => $cheque->factura->monto,
                        'no_doc' => $cheque->no_cheque,
                        'estado' => $cheque->estado,
                        'foto' => $cheque->foto_ch ? asset('storage/' . $cheque->foto_ch) : null
                    ]
                ];
            }
        }

        // Fetch Tarjetas (Estado 0 = Anulado, 1 = Activo/Pagado, 2 = Confirmado)
        $tarjetas = TarjetaCredito::with(['factura.empresa'])->whereIn('estado', [0, 1, 2])->get();

        foreach ($tarjetas as $tarjeta) {
            if ($tarjeta->factura && $tarjeta->factura->empresa) {
                // Determine Color
                if ($tarjeta->estado == 2) {
                    $color = '#9ca3af'; // Gray (Confirmed)
                } elseif ($tarjeta->estado == 0) {
                    $color = '#ef4444'; // Red (Voided)
                } else {
                    $color = '#a855f7'; // Purple (Pending)
                }

                $events[] = [
                    'title' => $tarjeta->factura->empresa->nombre_empresa . ' - Q.' . number_format($tarjeta->factura->monto, 2),
                    'start' => $tarjeta->fecha_pago,
                    'color' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'Tarjeta',
                        'id' => $tarjeta->id,
                        'factura_id' => $tarjeta->factura->id,
                        'monto' => $tarjeta->factura->monto,
                        'no_doc' => $tarjeta->no_autorizacion,
                        'estado' => $tarjeta->estado,
                        'foto' => $tarjeta->foto_tc ? asset('storage/' . $tarjeta->foto_tc) : null
                    ]
                ];
            }
        }

        // Fetch Facturas pagadas en Efectivo (Estado 2 = Pagada Efectivo)
        $facturasEfectivo = \App\Models\Factura::with(['empresa'])
            ->where('estado', 2)
            ->get();

        foreach ($facturasEfectivo as $factura) {
            if ($factura->empresa) {
                $events[] = [
                    'title' => $factura->empresa->nombre_empresa . ' - Q.' . number_format($factura->monto, 2),
                    'start' => $factura->updated_at->format('Y-m-d'), // Fecha de pago (cuando se actualizó)
                    'color' => '#9ca3af', // Gray (Confirmed/Completed)
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'Efectivo',
                        'id' => $factura->id,
                        'factura_id' => $factura->id,
                        'monto' => $factura->monto,
                        'no_doc' => 'N/A',
                        'estado' => 2, // Confirmado (ya pagado)
                        'foto' => $factura->foto_fac ? asset('storage/' . $factura->foto_fac) : null
                    ]
                ];
            }
        }

        // Fetch Depósitos (Estado 5 = Pagada con Depósito)
        $depositos = \App\Models\Deposito::with(['factura.empresa'])->get();

        foreach ($depositos as $deposito) {
            if ($deposito->factura && $deposito->factura->empresa) {
                $events[] = [
                    'title' => $deposito->factura->empresa->nombre_empresa . ' - Q.' . number_format($deposito->factura->monto, 2),
                    'start' => $deposito->fecha_deposito,
                    'color' => '#9ca3af', // Gray (Confirmed/Completed)
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'Depósito',
                        'id' => $deposito->id,
                        'factura_id' => $deposito->factura->id,
                        'monto' => $deposito->factura->monto,
                        'no_doc' => $deposito->no_deposito,
                        'estado' => 2, // Confirmado (ya pagado)
                        'foto' => $deposito->foto_deposito ? asset('storage/' . $deposito->foto_deposito) : null
                    ]
                ];
            }
        }

        return view('calendario.index', compact('events'));
    }

    public function confirmarCheque($id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->estado = 2; // Confirmado/Cobrado
        $cheque->save();

        return response()->json(['success' => true]);
    }

    public function confirmarTarjeta($id)
    {
        $tarjeta = TarjetaCredito::findOrFail($id);
        $tarjeta->estado = 2; // Confirmado/Acreditado
        $tarjeta->save();

        return response()->json(['success' => true]);
    }

    public function anularCheque($id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->estado = 0; // Anulado
        $cheque->save();

        // Reset Factura to Pendiente
        $factura = $cheque->factura;
        $factura->estado = 1; // Pendiente
        $factura->save();

        return response()->json(['success' => true]);
    }

    public function anularTarjeta($id)
    {
        $tarjeta = TarjetaCredito::findOrFail($id);
        $tarjeta->estado = 0; // Anulado
        $tarjeta->save();

        // Reset Factura to Pendiente
        $factura = $tarjeta->factura;
        $factura->estado = 1; // Pendiente
        $factura->save();

        return response()->json(['success' => true]);
    }
}
