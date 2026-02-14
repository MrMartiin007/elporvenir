<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosEnvio extends Model
{
    use HasFactory;

    protected $table = 'datos_envios';

    protected $fillable = [
        'pedidos_id',
        'numero_guia',
        'comprobante',
        'tipo_comprobante',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedidos_id');
    }
}
