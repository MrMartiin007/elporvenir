<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetalleVenta
 *
 * @property $id
 * @property $cantidad
 * @property $precio_unitario
 * @property $created_at
 * @property $updated_at
 * @property $productos_id
 * @property $ventas_id
 *
 * @property Producto $producto
 * @property Venta $venta
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DetalleVenta extends Model
{
    

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['cantidad', 'precio_unitario', 'productos_id', 'ventas_id','descuento'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'productos_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(\App\Models\Venta::class, 'ventas_id', 'id');
    }
    

}
