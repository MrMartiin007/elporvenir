<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarjetaCredito extends Model
{
    protected $table = 'tarjeta_credito';

    protected $fillable = ['no_autorizacion', 'foto_tc', 'facturas_id', 'estado', 'fecha_pago'];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'facturas_id');
    }
}
