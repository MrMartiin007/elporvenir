<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    protected $table = 'depositos';

    protected $fillable = ['no_deposito', 'fecha_deposito', 'foto_deposito', 'facturas_id'];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'facturas_id');
    }
}
