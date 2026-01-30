<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venta
 *
 * @property $id
 * @property $fecha_venta
 * @property $cantidad_productos
 * @property $total_vendido
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Venta extends Model
{


    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['fecha_venta', 'cantidad_productos', 'total_vendido', 'estado', 'users_id'];

    public function producto()
    {
        return $this->hasMany(Producto::class, 'id');
    }

    public function detalles()
    {
        return $this->hasMany(\App\Models\DetalleVenta::class, 'ventas_id');
    }
    // app/Models/Venta.php

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function codigosNoEncontrados()
    {
        return $this->hasMany(\App\Models\CodigoNoEncontrado::class, 'ventas_id');
    }



}
