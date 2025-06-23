<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto
 *
 * @property $id
 * @property $codigo_producto
 * @property $detalle_producto
 * @property $foto_producto
 * @property $precio_costo
 * @property $precio_venta
 * @property $precio_docena
 * @property $created_at
 * @property $updated_at
 * @property  $marcas_id
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Producto extends Model
{
    

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['codigo_producto', 'detalle_producto', 'foto_producto', 'precio_costo', 'precio_venta', 'precio_docena','marcas_id'];

public function marca()
{
    return $this->belongsTo(Marca::class, 'marcas_id');
}


}
