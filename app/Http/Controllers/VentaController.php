<?php

namespace App\Http\Controllers;

use App\Models\CodigoNoEncontrado;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use App\Http\Requests\VentaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index()
    {
        $ventaActiva = Venta::where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if ($ventaActiva) {
            return redirect()
                ->route('ventas.create', $ventaActiva->id);
        }

        // Si es superadmin, mostrar todas las ventas con relación de usuario
        if (auth()->user()->hasRole('superadmin')) {
            $ventas = Venta::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Usuario normal solo ve sus propias ventas
            $ventas = Venta::where('users_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('venta.index', compact('ventas'));
    }

    public function create(Request $request)
    {
        $productosFiltrados = collect();

        $venta = Venta::where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if (!$venta) {
            return redirect()->route('ventas.index')
                ->with('error', 'No hay una venta activa.');
        }

        if ($request->filled('scan')) {
            $codigo = $request->input('scan');
            $producto = Producto::with('ultimaEntrada')
                ->where('codigo_producto', $codigo)
                ->first();

            if (!$producto) {
                // Registrar el código no encontrado
                CodigoNoEncontrado::create([
                    'codigo' => $codigo,
                    'ventas_id' => $venta->id,
                ]);

                return redirect()->route('ventas.create')
                    ->with('bad_status', 'Producto no encontrado. Código registrado para revisión.');
            }

            $precioUnitario = optional($producto->ultimaEntrada)->precio_venta;

            if (!$precioUnitario) {
                return redirect()->route('ventas.create')
                    ->with('bad_status', 'El producto no tiene precio asignado.');
            }

            // Validacion de stock eliminada a peticion
            // if ($producto->stock < 1) { ... }

            DetalleVenta::create([
                'ventas_id' => $venta->id,
                'productos_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => $precioUnitario,
            ]);

            // Descontar stock (solo si es positivo, no permitir negativos en BD)
            if ($producto->stock > 0) {
                $producto->decrement('stock');
            }
            // Si es 0, se queda en 0.

            $venta->cantidad_productos = $venta->detalles()->sum('cantidad');
            $venta->total_vendido = $venta->detalles()
                ->sum(\DB::raw('(precio_unitario - IFNULL(descuento, 0)) * cantidad'));
            $venta->save();

            return redirect()->route('ventas.create')
                ->with('success', 'Producto agregado.');
        }

        if ($request->filled('buscar')) {
            $productosFiltrados = Producto::where('codigo_producto', $request->buscar)
                ->get();
        }

        // Cargar códigos no encontrados para mostrar en la vista
        $venta->load('codigosNoEncontrados');

        return view('venta.create', compact('venta', 'productosFiltrados'));
    }


    public function store(Request $request)
    {
        $venta = Venta::where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if (!$venta) {
            return redirect()->route('ventas.index')
                ->with('error', 'No hay una venta activa.');
        }

        $producto = Producto::with('ultimaEntrada')
            ->find($request->productos_id);

        if (!$producto) {
            return redirect()->back()
                ->with('error', 'Producto no encontrado.');
        }

        if (!$producto->ultimaEntrada || $producto->ultimaEntrada->precio_venta == null) {
            return redirect()->back()
                ->with('error', 'Este producto no tiene precio registrado en una entrada.');
        }
        // Validacion stock eliminada
        // if ($producto->stock < 1) { ... }

        $precioUnitario = $producto->ultimaEntrada->precio_venta;

        DetalleVenta::create([
            'ventas_id' => $venta->id,
            'productos_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => $precioUnitario,
        ]);

        // Descontar stock si es posible, sino dejar en 0
        if ($producto->stock > 0) {
            $producto->decrement('stock');
        }



        $venta->cantidad_productos = $venta->detalles()->sum('cantidad');
        $venta->total_vendido = $venta->detalles()
            ->sum(\DB::raw('(precio_unitario - IFNULL(descuento, 0)) * cantidad'));
        $venta->save();

        return redirect()->route('ventas.create')
            ->with('success', 'Producto agregado correctamente.');
    }

    public function show($id)
    {
        // Buscar la venta con sus detalles y productos relacionados
        $venta = Venta::with('detalles.producto')->findOrFail($id);

        return view('venta.show', compact('venta'));
    }


    public function edit($id)
    {
        $venta = Venta::find($id);
        return view('venta.edit', compact('venta'));
    }

    public function update(Request $request, $detalleId)
    {
        $request->validate([
            'cantidad' => 'nullable|integer|min:1',
            'descuento' => 'nullable|numeric|min:0',
        ]);

        $detalle = DetalleVenta::findOrFail($detalleId);
        $producto = $detalle->producto;

        if ($request->filled('descuento') && $request->descuento > $detalle->precio_unitario) {
            return back()->with('bad_status', 'El descuento no puede ser mayor que el precio del producto.');
        }

        if ($request->filled('cantidad')) {
            $nuevaCantidad = (int) $request->cantidad;
            $cantidadActual = (int) $detalle->cantidad;

            if ($nuevaCantidad !== $cantidadActual) {
                $dif = $nuevaCantidad - $cantidadActual;

                if ($dif > 0) {
                    // Si aumenta cantidad, verificamos cuanto stock real podemos descontar
                    // El usuario pidio permitir venta sin stock, manteniendo stock en 0 si no alcanza
                    if ($producto->stock > 0) {
                        $descontar = min($producto->stock, $dif);
                        $producto->stock -= $descontar;
                    }
                    // Si stock ya es 0 o menor, no hacemos nada al stock, pero permitimos el cambio en detalle
                } else {
                    $producto->stock += abs($dif);
                }
                $producto->save();

                $detalle->cantidad = $nuevaCantidad;
            }
        }

        if ($request->filled('descuento')) {
            $detalle->descuento = $request->descuento;
        }

        $detalle->save();

        $venta = $detalle->venta;
        $venta->cantidad_productos = $venta->detalles()->sum('cantidad');
        $venta->total_vendido = $venta->detalles()
            ->sum(\DB::raw('(precio_unitario - IFNULL(descuento,0)) * cantidad'));
        $venta->save();

        return back()->with('success', 'Detalle actualizado correctamente.');
    }



    public function destroy($id)
    {

        $detalle = DetalleVenta::findOrFail($id);

        $venta = $detalle->venta;
        $producto = $detalle->producto;


        $producto->stock += $detalle->cantidad;
        $producto->save();


        $detalle->delete();


        $venta->cantidad_productos = $venta->detalles()->sum('cantidad');
        $venta->total_vendido = $venta->detalles()
            ->sum(\DB::raw('(precio_unitario - IFNULL(descuento, 0)) * cantidad'));
        $venta->save();

        return redirect()->route('ventas.create')
            ->with('success', 'Producto eliminado y stock actualizado.');
    }


    public function iniciarVenta()
    {
        $venta = Venta::create([
            'fecha_venta' => now(),
            'cantidad_productos' => 0,
            'total_vendido' => 0,
            'estado' => 1,
            'users_id' => auth()->id(),
        ]);

        return redirect()->route('ventas.create');
    }

    public function cerrarVenta(Venta $venta)
    {
        $venta->estado = 0;
        $venta->save();

        return redirect()->route('ventas.index')
            ->with('success', 'La venta ha sido cerrada correctamente.');
    }

    public function reabrir(Venta $venta)
    {
        // Verificar si ya tiene una venta activa para evitar colisiones
        $ventaActiva = Venta::where('users_id', auth()->id())
            ->where('estado', 1)
            ->first();

        if ($ventaActiva) {
            return back()->with('error', 'Ya tienes una venta activa. Por favor ciérrala antes de reabrir esta.');
        }

        // Validar que la venta sea de hoy
        if (!\Carbon\Carbon::parse($venta->fecha_venta)->isToday()) {
            return back()->with('error', 'Solo se pueden reabrir ventas del día de hoy.');
        }

        $venta->estado = 1;
        $venta->save();

        return redirect()->route('ventas.create')
            ->with('success', 'Venta reabierta. Puedes continuar editándola.');
    }

    public function eliminarCodigoNoEncontrado($id)
    {
        $codigo = CodigoNoEncontrado::findOrFail($id);
        $codigo->delete();

        return redirect()->route('ventas.create')
            ->with('success', 'Código eliminado de la lista.');
    }
}


