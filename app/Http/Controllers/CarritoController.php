<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /**
     * Agregar producto al carrito
     */
    public function agregar(Request $request)
    {
        $producto = Producto::with('ultimaEntrada', 'marca')->findOrFail($request->producto_id);

        // Validar que tenga precio
        if (!$producto->ultimaEntrada || !$producto->ultimaEntrada->precio_venta) {
            return back()->with('error', 'Este producto no tiene precio disponible.');
        }

        $carrito = session()->get('carrito', []);
        $cantidad = $request->cantidad ?? 1;

        // Si ya existe, incrementar cantidad
        if (isset($carrito[$producto->id])) {
            $carrito[$producto->id]['cantidad'] += $cantidad;
        } else {
            // Agregar nuevo item
            $carrito[$producto->id] = [
                'nombre' => $producto->detalle_producto,
                'precio' => $producto->ultimaEntrada->precio_venta,
                'cantidad' => $cantidad,
                'imagen' => $producto->foto_producto,
                'marca' => $producto->marca->nombre_marca ?? 'General'
            ];
        }

        session()->put('carrito', $carrito);

        return back()->with('success', '¡Producto agregado al carrito!');
    }

    /**
     * Mostrar carrito
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);
        return view('carrito.index', compact('carrito'));
    }

    /**
     * Actualizar cantidad de un producto
     */
    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'cantidad' => 'required|integer|min:1'
        ]);

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$request->id])) {
            $carrito[$request->id]['cantidad'] = $request->cantidad;
            session()->put('carrito', $carrito);
            return back()->with('success', 'Cantidad actualizada');
        }

        return back()->with('error', 'Producto no encontrado en el carrito');
    }

    /**
     * Eliminar producto del carrito
     */
    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
            return back()->with('success', 'Producto eliminado del carrito');
        }

        return back()->with('error', 'Producto no encontrado');
    }

    /**
     * Vaciar todo el carrito
     */
    public function vaciar()
    {
        session()->forget('carrito');
        return back()->with('success', 'Carrito vaciado');
    }
}
