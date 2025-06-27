<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntradaRequest extends FormRequest
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
			'fecha_ingreso' => 'required|string',
			'cantidad' => 'required|string',
			'precio_costo' => 'required|string',
			'precio_venta' => 'required|string',
			'precio_docena' => 'required|string',
            'productos_id' => 'required',
        ];
    }
}
