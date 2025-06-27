<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Entrada
 *
 * @property $id
 * @property $fecha_ingreso
 * @property $cantidad
 * @property $precio_costo
 * @property $precio_venta
 * @property $precio_docena
 * @property $created_at
 * @property $updated_at
 * @property $productos_id
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Entrada extends Model
{
    

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['fecha_ingreso', 'cantidad', 'precio_costo', 'precio_venta', 'precio_docena', 'productos_id'];


        public function producto()
    {
        return $this->belongsTo(Producto::class, 'productos_id', 'id');
    }

 protected static function booted()
    {
        static::created(function ($entrada) {
            $producto = Producto::find($entrada->productos_id);
            if ($producto) {
                $producto->stock += $entrada->cantidad;
                $producto->save();
            }
        });
    }

}
