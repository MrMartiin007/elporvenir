<?php

namespace App\Http\Requests;

use App\Models\Producto;
use App\Rules\CodigoDisponible;
use App\Services\CodigosProducto;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Registrar y editar productos. Los códigos (el principal y los adicionales) deben ser únicos:
 * ningún código puede estar en dos productos, ni repetido dentro del formulario.
 */
class ProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Al editar, el producto no choca consigo mismo
        $producto = $this->route('producto');
        $ignorar = $producto instanceof Producto ? $producto->id : null;

        return [
            'codigo_producto' => ['required', 'string', 'max:100', new CodigoDisponible($ignorar)],
            'codigos_extra' => ['nullable', 'array', 'max:20'],
            'codigos_extra.*' => ['nullable', 'string', 'max:100', new CodigoDisponible($ignorar)],
            'detalle_producto' => 'required|string',
            'foto_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'marcas_id' => 'required|exists:marcas,id',
            'oferta' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_producto.required' => 'Escribe el código principal del producto.',
            'codigo_producto.max' => 'El código principal no puede tener más de 100 caracteres.',
            'codigos_extra.max' => 'Puedes agregar hasta 20 códigos adicionales.',
            'codigos_extra.*.max' => 'Cada código no puede tener más de 100 caracteres.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            CodigosProducto::validarLista($v, $this->input('codigo_producto'), (array) $this->input('codigos_extra', []));
        });
    }
}
