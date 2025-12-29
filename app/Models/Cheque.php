<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $table = 'cheques';

    protected $fillable = ['no_cheque', 'fecha_cobro', 'foto_ch', 'facturas_id', 'estado'];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'facturas_id');
    }
}
