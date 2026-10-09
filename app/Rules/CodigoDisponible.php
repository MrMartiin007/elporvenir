<?php

namespace App\Rules;

use App\Services\CodigosProducto;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * El código no debe estar usado por ningún otro producto, ni como principal ni como adicional.
 */
class CodigoDisponible implements ValidationRule
{
    public function __construct(private ?int $ignorarProductoId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $codigo = trim((string) $value);
        if ($codigo === '') {
            return;
        }

        $dueno = CodigosProducto::duenoDe($codigo, $this->ignorarProductoId);
        if ($dueno) {
            $fail(CodigosProducto::mensajeEnUso($codigo, $dueno));
        }
    }
}
