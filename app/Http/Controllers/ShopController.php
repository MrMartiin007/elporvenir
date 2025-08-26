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
    
        $productos = Producto::with(['marca', 'ultimaEntrada'])
        ->where('detalle_producto', 'like', '%' . $search . '%')
        ->orWhereHas('marca', function ($query) use ($search) {
            $query->where('nombre_marca', 'LIKE', "%$search%");
        })
                            ->orderBy('created_at', 'desc')
                            ->get();
    
        return view('shop', compact('productos'));
    }
}