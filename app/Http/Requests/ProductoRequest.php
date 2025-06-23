<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'codigo_producto' => 'required|string',
            'detalle_producto' => 'required|string',
            'foto_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'precio_costo' => 'required|string',
            'precio_venta' => 'required|string',
            'precio_docena' => 'required|string',
            'marcas_id' => 'required|string',
        ];
    }
}
