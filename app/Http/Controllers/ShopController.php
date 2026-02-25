<?php

namespace App\Http\Controllers;


use App\Models\Producto;
use App\Models\Marca;
use App\Helpers\IdObfuscator;
use Carbon\Carbon;

use App\Models\Factura;

use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function shop(Request $request)
    {
        $search = $request->input('search');
        $marcaId = $request->input('marca');

        $query = Producto::with(['marca', 'ultimaEntrada'])
            ->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('detalle_producto', 'like', '%' . $search . '%')
                        ->orWhereHas('marca', function ($subQ) use ($search) {
                            $subQ->where('nombre_marca', 'LIKE', "%$search%");
                        });
                }
            });

        if ($marcaId) {
            $query->where('marcas_id', $marcaId);
        }

        // Sorting
        $sort = $request->input('sort');

        // Actually, let's stick to the previous 'ultimaEntrada' logic. sorting by a hasOne latest relation requires join.
        // Simplified approach for now:
        switch ($sort) {
            case 'price_asc':
                $query->join('entradas', function ($join) {
                    $join->on('entradas.productos_id', '=', 'productos.id')
                        ->whereRaw('entradas.id = (select id from entradas where entradas.productos_id = productos.id order by created_at desc limit 1)');
                })
                    ->orderByDesc('productos.oferta')
                    ->orderBy('entradas.precio_venta', 'asc')
                    ->select('productos.*');
                break;
            case 'price_desc':
                $query->join('entradas', function ($join) {
                    $join->on('entradas.productos_id', '=', 'productos.id')
                        ->whereRaw('entradas.id = (select id from entradas where entradas.productos_id = productos.id order by created_at desc limit 1)');
                })
                    ->orderByDesc('productos.oferta')
                    ->orderBy('entradas.precio_venta', 'desc')
                    ->select('productos.*');
                break;
            case 'oldest':
                $query->orderByDesc('oferta')->orderBy('updated_at', 'asc');
                break;
            default: // newest
                $query->orderByDesc('oferta')->orderBy('updated_at', 'desc');
                break;
        }

        $productos = $query->paginate(12);  // 12 products: 6 rows of 2 (mobile) or 4 rows of 3 (desktop)
        $marcas = Marca::has('productos')->withCount('productos')->get();

        // Contador del carrito para el navbar
        $carritoCount = collect(session()->get('carrito', []))->sum('cantidad');

        return view('shop', compact('productos', 'marcas', 'search', 'marcaId', 'sort', 'carritoCount'));
    }

    public function showProduct($hash, $slug = null)
    {
        $id = IdObfuscator::decode($hash);

        if (!$id) {
            abort(404);
        }

        $producto = Producto::with(['marca', 'ultimaEntrada', 'entradas'])->findOrFail($id);

        // Productos relacionados (misma marca, excluyendo el actual)
        $relacionados = collect();
        if ($producto->marcas_id) {
            $relacionados = Producto::with(['marca', 'ultimaEntrada'])
                ->where('marcas_id', $producto->marcas_id)
                ->where('id', '!=', $producto->id)
                ->whereHas('ultimaEntrada')
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        $carritoCount = collect(session()->get('carrito', []))->sum('cantidad');
        $enCarrito = session('carrito') && isset(session('carrito')[$producto->id]);

        return view('product-detail', compact('producto', 'relacionados', 'carritoCount', 'enCarrito'));
    }

    public function contact()
    {
        return view('contact');
    }
}