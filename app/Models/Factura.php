<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Factura
 *
 * @property $id
 * @property $numero_factura
 * @property $monto
 * @property $foto_fac
 * @property $empresas_id
 * @property $estado
 * @property $created_at
 * @property $updated_at
 *
 * @property Empresa $empresa
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Factura extends Model
{

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['numero_factura', 'monto', 'foto_fac', 'empresas_id', 'estado'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Models\Empresa', 'empresas_id', 'id');
    }

    public function cheques()
    {
        return $this->hasMany(Cheque::class, 'facturas_id');
    }

    public function cheque()
    {
        return $this->hasOne(Cheque::class, 'facturas_id')->latestOfMany();
    }

    public function tarjetasCredito()
    {
        return $this->hasMany(TarjetaCredito::class, 'facturas_id');
    }

    public function tarjetaCredito()
    {
        return $this->hasOne(TarjetaCredito::class, 'facturas_id')->latestOfMany();
    }

    public function deposito()
    {
        return $this->hasOne(Deposito::class, 'facturas_id')->latestOfMany();
    }

}
