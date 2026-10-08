<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Código de barras adicional de un producto. El principal vive en productos.codigo_producto.
 *
 * @property int $id
 * @property int $productos_id
 * @property string $codigo
 */
class ProductoCodigo extends Model
{
    protected $table = 'producto_codigos';

    protected $fillable = ['productos_id', 'codigo'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productos_id');
    }
}
