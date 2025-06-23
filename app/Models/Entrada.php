<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Entrada
 *
 * @property $id
 * @property $fecha_ingreso
 * @property $cantidad
 * @property $created_at
 * @property $updated_at
 * @property $productos_id
 *
 * @property Producto $producto
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
    protected $fillable = ['fecha_ingreso', 'cantidad', 'productos_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
