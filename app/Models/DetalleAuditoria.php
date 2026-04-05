<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleAuditoria extends Model
{
    protected $table = 'detalle_auditorias';

    protected $fillable = [
        'auditorias_id',
        'productos_id',
        'user_id',
        'stock_anterior',
        'stock_nuevo',
        'precio_costo_anterior',
        'precio_costo_nuevo',
        'precio_venta_anterior',
        'precio_venta_nuevo',
        'precio_docena_anterior',
        'precio_docena_nuevo',
    ];

    public function auditoria()
    {
        return $this->belongsTo(Auditoria::class, 'auditorias_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productos_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
