<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $fillable = [
        'pedidos_id',
        'productos_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedidos_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productos_id');
    }
}
