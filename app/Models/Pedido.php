<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'nombre_cliente',
        'telefono_cliente',
        'email_cliente',
        'direccion_cliente',
        'departamento_id',
        'municipio_id',
        'notas_cliente',
        'subtotal',
        'descuento',
        'total',
        'envio',
        'cantidad_productos',
        'estado',
        'numero_pedido'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedidos_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function datosEnvio()
    {
        return $this->hasOne(DatosEnvio::class, 'pedidos_id');
    }

    // Generar número de pedido único
    public static function generarNumeroPedido()
    {
        $fecha = now()->format('Ymd');
        $ultimo = self::whereDate('created_at', today())->count() + 1;
        return 'PED-' . $fecha . '-' . str_pad($ultimo, 4, '0', STR_PAD_LEFT);
    }
}
