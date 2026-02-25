<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoEliminadoVenta extends Model
{
    protected $table = 'producto_eliminado_ventas';

    protected $fillable = [
        'ventas_id',
        'productos_id',
        'users_id',
        'cantidad',
        'precio_unitario',
        'importe_total'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ventas_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productos_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
