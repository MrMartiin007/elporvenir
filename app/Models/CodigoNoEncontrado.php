<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CodigoNoEncontrado
 *
 * @property $id
 * @property $codigo
 * @property $ventas_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Venta $venta
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CodigoNoEncontrado extends Model
{
    protected $table = 'codigos_no_encontrados';

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['codigo', 'ventas_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(\App\Models\Venta::class, 'ventas_id', 'id');
    }
}
