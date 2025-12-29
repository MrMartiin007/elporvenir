<?php

namespace App\Http\Controllers;


use App\Models\Producto;
use App\Models\Marca;
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
        if ($sort == 'price_asc') {
            // Join with entradas to sort by price
            $query->select('productos.*')
                ->join('entradas', 'productos.id', '=', 'entradas.productos_id') // Assuming FK exists or logic allows
                ->orderBy('entradas.precio_venta', 'asc')
                ->distinct();
            // Note: this is tricky if multiple entries. 
            // A better approach for the 'latest' price is complex in SQL without subqueries or window functions.
            // Given the structure, let's try a simpler approach if the relationship 'ultimaEntrada' is loaded.
            // Eloquent sorting by related model attribute is hard.
            // Let's use a subquery sort or simple join if possible.
        }

        // Actually, let's stick to the previous 'ultimaEntrada' logic. sorting by a hasOne latest relation requires join.
        // Simplified approach for now:
        switch ($sort) {
            case 'price_asc':
                $query->join('entradas', function ($join) {
                    $join->on('entradas.productos_id', '=', 'productos.id')
                        ->whereRaw('entradas.id = (select id from entradas where entradas.productos_id = productos.id order by created_at desc limit 1)');
                })
                    ->orderBy('entradas.precio_venta', 'asc')
                    ->select('productos.*');
                break;
            case 'price_desc':
                $query->join('entradas', function ($join) {
                    $join->on('entradas.productos_id', '=', 'productos.id')
                        ->whereRaw('entradas.id = (select id from entradas where entradas.productos_id = productos.id order by created_at desc limit 1)');
                })
                    ->orderBy('entradas.precio_venta', 'desc')
                    ->select('productos.*');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $productos = $query->paginate(9);
        $marcas = Marca::has('productos')->get();

        return view('shop', compact('productos', 'marcas', 'search', 'marcaId', 'sort'));
    }

    public function contact()
    {
        return view('contact');
    }
}