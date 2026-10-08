<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /**
     * Cantidad máxima por producto en una sola línea del carrito.
     */
    private const MAX_CANTIDAD = 99;

    /**
     * Agregar producto al carrito
     */
    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'cantidad' => 'nullable|integer|min:1|max:' . self::MAX_CANTIDAD,
        ]);

        $producto = Producto::with('ultimaEntrada', 'marca')->findOrFail($request->producto_id);

        // Validar que tenga precio
        if (!$producto->ultimaEntrada || !$producto->ultimaEntrada->precio_venta) {
            return $this->respuesta($request, false, 'Este producto no tiene precio disponible.');
        }

        // Sin stock no se puede comprar (la vista ya lo oculta, aquí se exige en servidor)
        $stock = (int) $producto->stock;
        if ($stock <= 0) {
            return $this->respuesta($request, false, 'Este producto está agotado.');
        }

        $carrito = session()->get('carrito', []);
        $cantidad = (int) ($request->cantidad ?? 1);
        $enCarrito = $carrito[$producto->id]['cantidad'] ?? 0;
        $nueva = min($enCarrito + $cantidad, self::MAX_CANTIDAD);

        if ($nueva > $stock) {
            $nueva = $stock;
            $aviso = "Solo hay {$stock} unidad(es) disponibles de este producto.";
        }

        $carrito[$producto->id] = [
            'nombre' => $producto->detalle_producto,
            'precio' => $producto->ultimaEntrada->precio_venta,
            'cantidad' => $nueva,
            'imagen' => $producto->foto_producto,
            'marca' => $producto->marca->nombre_marca ?? 'General'
        ];

        session()->put('carrito', $carrito);

        return $this->respuesta($request, true, $aviso ?? '¡Producto agregado al carrito!');
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
            'cantidad' => 'required|integer|min:1|max:' . self::MAX_CANTIDAD
        ]);

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$request->id])) {
            $cantidad = (int) $request->cantidad;
            $stock = (int) Producto::whereKey($request->id)->value('stock');

            if ($cantidad > $stock) {
                $cantidad = max($stock, 1);
                $carrito[$request->id]['cantidad'] = $cantidad;
                session()->put('carrito', $carrito);
                return $this->respuesta($request, false, "Solo hay {$stock} unidad(es) disponibles de este producto.");
            }

            $carrito[$request->id]['cantidad'] = $cantidad;
            session()->put('carrito', $carrito);
            return $this->respuesta($request, true, 'Cantidad actualizada');
        }

        return $this->respuesta($request, false, 'Producto no encontrado en el carrito');
    }

    /**
     * Resumen del carrito en JSON (mini-carrito lateral)
     */
    public function resumen()
    {
        return response()->json($this->datosCarrito());
    }

    /**
     * Eliminar producto del carrito
     */
    public function eliminar(Request $request, $id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
            return $this->respuesta($request, true, 'Producto eliminado del carrito');
        }

        return $this->respuesta($request, false, 'Producto no encontrado');
    }

    /**
     * Vaciar todo el carrito
     */
    public function vaciar(Request $request)
    {
        session()->forget('carrito');
        return $this->respuesta($request, true, 'Carrito vaciado');
    }

    /**
     * Responde en JSON (fetch/AJAX) o con redirect (formulario clásico).
     */
    private function respuesta(Request $request, bool $ok, string $mensaje)
    {
        if ($request->expectsJson()) {
            return response()->json(
                ['ok' => $ok, 'message' => $mensaje] + $this->datosCarrito(),
                $ok ? 200 : 422
            );
        }

        return back()->with($ok ? 'success' : 'error', $mensaje);
    }

    /**
     * Estado actual del carrito listo para el frontend.
     */
    private function datosCarrito(): array
    {
        $items = [];
        $subtotal = 0;
        $count = 0;

        foreach (session('carrito', []) as $id => $item) {
            $precio = (float) $item['precio'];
            $cantidad = (int) $item['cantidad'];
            $linea = round($precio * $cantidad, 2);

            $items[] = [
                'id' => $id,
                'nombre' => $item['nombre'],
                'marca' => $item['marca'] ?? '',
                'precio' => $precio,
                'cantidad' => $cantidad,
                'subtotal' => $linea,
                'imagen' => !empty($item['imagen']) ? asset('storage/' . $item['imagen']) : asset('logo.jpg'),
            ];
            $subtotal += $linea;
            $count += $cantidad;
        }

        return [
            'count' => $count,
            'subtotal' => round($subtotal, 2),
            'items' => $items,
        ];
    }
}
