<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Helpers\IdObfuscator;

/**
 * Class Producto
 *
 * @property $id
 * @property $codigo_producto
 * @property $detalle_producto
 * @property $foto_producto
 * @property $created_at
 * @property $updated_at
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
    protected $fillable = ['codigo_producto', 'detalle_producto', 'foto_producto', 'marcas_id'];


    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marcas_id');
    }
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'productos_id');
    }

    public function ultimaEntrada()
    {
        return $this->hasOne(Entrada::class, 'productos_id')->latestOfMany();
    }

    public function getSlugAttribute()
    {
        $marca = $this->marca ? $this->marca->nombre_marca : '';
        return Str::slug($marca . '-' . $this->detalle_producto);
    }

    public function getHashIdAttribute()
    {
        return IdObfuscator::encode($this->id);
    }

}
